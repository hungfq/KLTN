<?php

namespace App\Modules\Schedule\Actions;

use App\Entities\Schedule;
use App\Entities\User;
use App\Events\SendEmailEvent;
use App\Exceptions\UserException;
use App\Mail\EmailForQueuing;

class ScheduleNotifyLecturerAction
{
    public $schedule;
    public $topics;
    public $listLecturerIds;

    public function handle($id)
    {
        $this->schedule = Schedule::find($id);

        $this->checkData()
            ->sendMailToLecturer();
    }

    protected function checkData()
    {
        if (!$this->schedule) {
//            throw new UserException("Schedule not found!");
            throw new UserException('Đợt đăng ký không tồn tại!', 400);
        }

        $this->topics = $this->schedule->topics()
            ->with('committee')
            ->where('lecturer_approved', 1)
            ->where('critical_approved', 1)
            ->whereHas('committee')
            ->get();
        $lecturer = $this->topics->pluck('lecturer_id')->toArray();
        $critical = $this->topics->pluck('critical_id')->toArray();
        $president = $this->topics->pluck('committee.president_id')->toArray();
        $secretary = $this->topics->pluck('committee.secretary_id')->toArray();

        $this->listLecturerIds = array_unique(array_merge($lecturer, $critical, $president, $secretary));

        return $this;
    }

    protected function sendMailToLecturer()
    {
        foreach ($this->listLecturerIds as $lecturer) {
            $this->sendingMail($lecturer);
        }
    }

    protected function sendingMail($lecturerId)
    {
        $lecturer = User::find($lecturerId);
        if (!$lecturer) {
//            throw new UserException("User not found!");
            throw new UserException('Người dùng không tồn tại!', 400);
        }

        $topics = $this->topics->filter(function ($topic) use ($lecturerId) {
            return $topic->lecturer_id == $lecturerId ||
                $topic->critical_id == $lecturerId ||
                data_get($topic, 'committee.president_id') == $lecturerId ||
                data_get($topic, 'committee.secretary_id') == $lecturerId;

        });

        $dataEmail = [
            'lecturer' => $lecturer,
            'topics' => $topics,
        ];
        event(new SendEmailEvent([
            'email' => data_get($lecturer, 'email'),
            'email_body' => new EmailForQueuing('LỊCH NGỒI HỘI ĐỒNG', $dataEmail, 'MailScheduleForLecturer'),
        ]));

    }
}