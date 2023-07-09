<?php

namespace App\Modules\Task\Actions;

use App\Entities\Task;
use App\Entities\TaskComment;
use App\Entities\User;
use App\Exceptions\UserException;
use App\Libraries\Socket;
use App\Modules\Task\DTO\TaskCommentAddDTO;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class TaskCommentAddAction
{
    public TaskCommentAddDTO $dto;
    public $task;
    public $userAddNotify = [];

    /***
     * @param TaskCommentAddDTO $dto
     * @return void
     */
    public function handle($dto)
    {
        $this->dto = $dto;

        $this->checkData()
            ->createComment()
            ->addNotification();
    }

    protected function checkData()
    {
        $this->task = Task::query()->find($this->dto->id);
        if (!$this->task) {
//            throw new UserException('Task not found');
            throw new UserException('Nhiệm vụ không tồn tại trong hệ thống!', 400);
        }

        return $this;
    }

    protected function createComment()
    {
        $comment = new TaskComment();
        $comment->message = $this->dto->message;
        $this->task->comments()->save($comment);

        $members = data_get($this->task, 'topic.students', []);
        $ids = $members->pluck('id');

        $ids[] = data_get($this->task, 'topic.lecturer_id');
        if ($ids instanceof Collection) {
            $ids = $ids->toArray();
        }
        Socket::sendUpdateTaskInfoRequest($ids, $this->task->id);

        $this->userAddNotify = $ids;

        return $this;
    }

    protected function addNotification()
    {
        if (($key = array_search(Auth::id(), $this->userAddNotify)) !== false) {
            unset($this->userAddNotify[$key]);
        }

        $title = data_get($this->task, 'title');
        $users = User::query()
            ->whereIn('id', $this->userAddNotify)
            ->get();

        if($users) {
            $users->each(function ($user) use ($title) {
                $data = [
                    'title' => 'CÓ BÌNH LUẬN MỚI',
                    'message' => "Nhiệm vụ: \"$title\" có bình luận mới",
                ];
                $user->notifications()->create($data);
                Socket::sendUpdateNotificationRequest([data_get($user, 'id')], $data);
            });
        }

        return $this;
    }
}
