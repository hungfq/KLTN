<?php

namespace App\Modules\Topic\Actions;

use App\Entities\Topic;
use App\Exceptions\UserException;
use App\Libraries\Socket;
use Illuminate\Support\Facades\Auth;

class TopicLecturerApproveAction
{
    public $id;
    public $topic;

    /***
     * @return void
     */
    public function handle($id)
    {
        $this->id = $id;

        $this->checkData()
            ->approve()
            ->addNotification();
    }

    protected function checkData()
    {
        $this->topic = Topic::find($this->id);
        if (!$this->topic) {
//            throw new UserException("Topic not found!");
            throw new UserException('Đề tài không tồn tại trong hệ thống!', 400);
        }

        if ($this->topic->lecturer_id != Auth::id()) {
//            throw new UserException("You are not as advisor this topic!");
            throw new UserException('Bạn không phải GVHD của đề tài này!', 400);
        }

        if ($this->topic->lecturer_approved) {
//            throw new UserException("Topic already approved!");
            throw new UserException('Đề tài đã được chấp thuận!', 400);
        }

        return $this;
    }

    protected function approve()
    {
        $this->topic->lecturer_approved = true;
        $this->topic->save();

        return $this;
    }

    protected function addNotification()
    {
        $title = data_get($this->topic, 'title');

        $this->topic->students->each(function ($student) use ($title) {
            $data = [
                'title' => 'DUYỆT HỘI ĐỒNG',
                'message' => "Đề tài $title đã GVHD chấp thuận.",
            ];
            $student->notifications()->create($data);
            Socket::sendUpdateNotificationRequest([data_get($student, 'id')], $data);
        });

        return $this;
    }
}
