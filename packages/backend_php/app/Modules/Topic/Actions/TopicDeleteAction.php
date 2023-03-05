<?php

namespace App\Modules\Topic\Actions;

use App\Entities\Schedule;
use App\Entities\Topic;
use App\Exceptions\UserException;

class TopicDeleteAction
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
            ->delete();
    }

    protected function checkData()
    {
        $this->topic = Topic::find($this->id);
        if (!$this->topic) {
            throw new UserException("Topic not found!");
        }

        return $this;
    }

    protected function delete()
    {
        $this->topic->students()->sync([]);
        $this->topic->delete();
        return $this;
    }
}
