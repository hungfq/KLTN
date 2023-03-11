<?php

namespace App\Modules\Topic\Actions;

use App\Entities\Topic;
use App\Exceptions\UserException;
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
            throw new UserException("Topic not found!");
        }

        if ($this->topic->lecturer_id != Auth::id()) {
            throw new UserException("You are not as advisor this topic!");
        }

        if ($this->topic->lecturer_approved) {
            throw new UserException("Topic already approved!");
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
            $student->notifications()->create([
                'title' => 'DUYỆT HỘI ĐỒNG',
                'message' => "Đề tài $title đã GVHD chấp thuận.",
            ]);
        });

        return $this;
    }
}
