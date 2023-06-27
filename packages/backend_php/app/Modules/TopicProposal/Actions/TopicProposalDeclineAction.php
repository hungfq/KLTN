<?php

namespace App\Modules\TopicProposal\Actions;

use App\Entities\TopicProposal;
use App\Events\SendEmailEvent;
use App\Exceptions\UserException;
use App\Libraries\Socket;
use App\Mail\EmailForQueuing;

class TopicProposalDeclineAction
{
    public $id;
    public $topicProposal;

    public function handle($id)
    {
        $this->id = $id;

        $this->checkData()
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

        return $this;
    }

    protected function addNotification()
    {
        $title = data_get($this->topicProposal, 'title');

        $this->topicProposal->students->each(function ($student) use ($title) {

            $data = [
                'title' => 'DUYỆT YÊU CẦU HƯỚNG DẪN',
                'message' => "Đề tài $title đã bị từ chối.",
            ];
            $student->notifications()->create($data);
            Socket::sendUpdateNotificationRequest([data_get($student, 'id')], $data);

            $dataEmail = [
                'topic' => $this->topicProposal,
                'student' => $student,
            ];
            event(new SendEmailEvent([
                'email' => data_get($student, 'email'),
                'email_body' => new EmailForQueuing('PHẢN HỒI ĐỀ TÀI HƯỚNG DẪN', $dataEmail, 'MailProposalDeclineToStudent'),
            ]));
        });

        return $this;
    }

    protected function deleteProposal()
    {
        $this->topicProposal->delete();

        return $this;
    }
}
