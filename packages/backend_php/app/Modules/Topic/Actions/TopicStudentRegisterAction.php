<?php

namespace App\Modules\Topic\Actions;

use App\Entities\Topic;
use App\Exceptions\UserException;
use App\Libraries\Socket;
use Illuminate\Support\Facades\Auth;

class TopicStudentRegisterAction
{
    public $id;
    public $topic;
    public $topics;
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

        $this->topics = $this->schedule->topics;
        foreach ($this->topics as $topic) {
            if ($topic->students->contains(Auth::id())) {
                throw new UserException("You are already register!");
            }
        }

        if ($this->topic->students->count() >= $this->topic->limit) {
            throw new UserException("The register is out of limit!");
        }

        return $this;
    }

    protected function register()
    {
        $this->topic->students()->attach(Auth::id());

        return $this;
    }

    protected function addNotification()
    {
        if ($lecturer = data_get($this->topic, 'lecturer')) {
            $topicCode = data_get($this->topic, 'code');

            $data = [
                'title' => 'ĐĂNG KÝ ĐỀ TÀI',
                'message' => "Có đăng ký mới trong đề tài: $topicCode",
            ];

            $lecturer->notifications()->create($data);

            Socket::sendUpdateNotificationRequest([data_get($lecturer, 'id')], $data);
        }
    }
}
