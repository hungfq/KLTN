<?php

namespace App\Modules\Topic\Actions;

use App\Entities\Topic;
use App\Exceptions\UserException;
use App\Libraries\Socket;
use Illuminate\Support\Facades\Auth;

class TopicCriticalDeclineAction
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

        if ($this->topic->critical_id != Auth::id()) {
//            throw new UserException("You are not as critical this topic!");
            throw new UserException('Bạn không phải GVPB của đề tài này!', 400);
        }

        return $this;
    }

    protected function approve()
    {
        $this->topic->critical_approved = false;
        $this->topic->save();

        return $this;
    }

    protected function addNotification()
    {
        $title = data_get($this->topic, 'title');

        $this->topic->students->each(function ($student) use ($title) {
            $data = [
                'title' => 'DUYỆT HỘI ĐỒNG',
                'message' => "Đề tài $title GVPB đã từ chối ra hội đồng.",
            ];
            $student->notifications()->create($data);
            Socket::sendUpdateNotificationRequest([data_get($student, 'id')], $data);
        });

        return $this;
    }
}
