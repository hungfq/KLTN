<?php

namespace App\Modules\TopicProposal\Actions;

use App\Entities\Topic;
use App\Entities\TopicProposal;
use App\Entities\User;
use App\Events\SendEmailEvent;
use App\Exceptions\UserException;
use App\Libraries\Socket;
use App\Mail\EmailForQueuing;

class TopicProposalApproveAction
{
    public $id;
    public $studentIds;
    public $topicProposal;

    public function handle($id)
    {
        $this->id = $id;

        $this->checkData()
            ->createTopic()
            ->addNotification()
            ->deleteProposal();
    }

    protected function checkData()
    {
        $this->topicProposal = TopicProposal::query()->find($this->id);
        if (!$this->topicProposal) {
//            throw new UserException('Topic proposal not found');
            throw new UserException('Đề xuất không tồn tại trong hệ thống!', 400);
        }

        $this->studentIds = $this->topicProposal->students->map(function ($student) {
            return $student->id;
        });

        $studentAlreadyRegistered = Topic::query()
            ->where('schedule_id', $this->topicProposal->schedule_id)
            ->whereHas('students', function ($q) {
                $q->whereIn('id', $this->studentIds);
            })->exists();
        if ($studentAlreadyRegistered) {
//            throw new UserException('Some student already has register in another topic');
            throw new UserException('Sinh viên đã đăng ký đề tài khác!', 400);
        }

        return $this;
    }

    protected function createTopic()
    {
        $topicData = $this->topicProposal->replicate();
        $topicData = $topicData->toArray();
        $topicData['code'] = Topic::generateTopicCode($this->topicProposal->schedule);
        $topic = Topic::create($topicData);
        $topic->students()->sync($this->studentIds);


        return $this;
    }

    protected function addNotification()
    {
        $title = data_get($this->topicProposal, 'title');

        $this->topicProposal->students->each(function ($student) use ($title) {
            $data = [
                'title' => 'DUYỆT YÊU CẦU',
                'message' => "Đề tài $title đã được chấp thuận.",
            ];
            $student->notifications()->create($data);
            Socket::sendUpdateNotificationRequest([data_get($student, 'id')], $data);
        });

        return $this;
    }

    protected function deleteProposal()
    {
        $this->topicProposal->delete();

        foreach ($this->studentIds as $studentId) {
            $proposals = TopicProposal::query()
                ->where('schedule_id', $this->topicProposal->schedule_id)
                ->whereHas('students', function ($q) use ($studentId) {
                    $q->where('id', $studentId);
                })->get();
            foreach ($proposals as $proposal) {
                $student = User::find($studentId);

                if ($proposal->students()->count() == 1) {
                    $dataEmail = [
                        'topic' => $proposal,
                        'student' => $student,
                    ];
                    event(new SendEmailEvent([
                        'email' => data_get($student, 'email'),
                        'email_body' => new EmailForQueuing('HỦY ĐỀ TÀI', $dataEmail, 'MailProposalDeletedToStudent'),
                    ]));

                    $proposal->delete();
                } else {
                    $dataEmail = [
                        'topic' => $proposal,
                        'student' => $student,
                    ];
                    event(new SendEmailEvent([
                        'email' => data_get($student, 'email'),
                        'email_body' => new EmailForQueuing('HỦY ĐĂNG KÝ KHỎI ĐỀ TÀI', $dataEmail, 'MailProposalDeletedToStudent'),
                    ]));

                    $proposal->students()->where('id', $studentId)->delete();
                }
            }
        }

        return $this;
    }
}
