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
            ->approve();
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
    }
}
