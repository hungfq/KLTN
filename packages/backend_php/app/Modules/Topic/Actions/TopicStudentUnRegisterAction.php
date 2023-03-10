<?php

namespace App\Modules\Topic\Actions;

use App\Entities\Topic;
use App\Exceptions\UserException;
use App\Libraries\Socket;
use Illuminate\Support\Facades\Auth;

class TopicStudentUnRegisterAction
{
    public $id;
    public $topic;
    public $schedule;

    /***
     * @return void
     */
    public function handle($id)
    {
        $this->id = $id;

        $this->checkData()
            ->register()
            ->addNotification();
    }

    protected function checkData()
    {
        $this->topic = Topic::find($this->id);
        if (!$this->topic) {
            throw new UserException("Topic not found!");
        }

        if (!$this->topic->schedule) {
            throw new UserException("Schedule not found!");
        }

        $this->schedule = $this->topic->schedule;
        if (!$this->schedule->students->contains(Auth::id())) {
            throw new UserException("You are not in schedule!");
        }

        if (!($this->schedule->register_start <= date('Y-m-d H:i:s', time())
            && $this->schedule->register_end >= date('Y-m-d H:i:s', time()))) {
            throw new UserException("Schedule is not in register time!");
        }

//        if ($this->topic->students->count() >= $this->topic->limit) {
//            throw new UserException("The register is out of limit!");
//        }

        if (!$this->topic->students->contains(Auth::id())) {
            throw new UserException("You are not register!");
        }

        return $this;
    }

    protected function register()
    {
        $this->topic->students()->detach(Auth::id());

        return $this;
    }

    protected function addNotification()
    {
        if ($lecturer = data_get($this->topic, 'lecturer')) {
            $topicCode = data_get($this->topic, 'code');

            $lecturer->notifications()->create([
                'title' => 'ĐĂNG KÝ ĐỀ TÀI',
                'message' => "Hủy đăng ký mới trong đề tài: $topicCode",
            ]);

            Socket::sendUpdateNotificationRequest([data_get($lecturer, 'id')]);
        }
    }
}
