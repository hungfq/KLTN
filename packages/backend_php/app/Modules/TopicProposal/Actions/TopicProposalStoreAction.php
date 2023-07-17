<?php

namespace App\Modules\TopicProposal\Actions;

use App\Entities\Document;
use App\Entities\Role;
use App\Entities\Schedule;
use App\Entities\TopicProposal;
use App\Entities\User;
use App\Events\SendEmailEvent;
use App\Exceptions\UserException;
use App\Libraries\Socket;
use App\Mail\EmailForQueuing;
use App\Modules\TopicProposal\DTO\TopicProposalStoreDTO;
use Illuminate\Support\Facades\Storage;

class TopicProposalStoreAction
{
    const SOURCE_UPLOAD = 'documents';

    public TopicProposalStoreDTO $dto;
    public $topic;
    public $studentIds;
    public $lecturer;

    /***
     * @param TopicProposalStoreDTO $dto
     * @return void
     */
    public function handle($dto)
    {
        $this->dto = $dto;

        $this->checkData()
            ->createTopic()
            ->storeFileIfExist()
            ->addNotification();
    }

    protected function checkData()
    {
        foreach (data_get($this->dto, 'students', []) as $studentCode) {
            $student = User::role(Role::ROLE_STUDENT)->where('code', $studentCode)->first();
            if (!$student) {
//                throw new UserException("Student not found!");
                throw new UserException('Sinh viên không tồn tại trong hệ thống!', 400);
            }
            $this->studentIds[] = $student->id;
        }

        if ($lecturerId = $this->dto->lecturer_id) {
            $this->lecturer = User::role(Role::ROLE_LECTURER)->find($lecturerId);
            if (!$this->lecturer) {
//                throw new UserException("Lecturer not found!");
                throw new UserException('GVHD không tồn tại trong hệ thống!', 400);
            }
        }

        $schedule = Schedule::query()->find($this->dto->schedule_id);
        if (!$schedule) {
//            throw new UserException("Schedule not found!");
            throw new UserException('Đợt đăng ký không tồn tại trong hệ thống!', 400);
        }

        if (!$this->dto->code) {
            $this->dto->code = TopicProposal::generateTopicCode($schedule);
        }

        return $this;
    }

    protected function createTopic()
    {
        $this->topic = TopicProposal::create($this->dto->all());
        $this->topic->students()->sync($this->studentIds);
        $this->topic->save();
        return $this;
    }

    protected function storeFileIfExist()
    {
        foreach ($this->dto->files as $index => $input) {
            $file = request()->file("files.$index");

            $params['file_name'] = $file->getClientOriginalName();
            $params['file_extension'] = $file->getClientOriginalExtension();
            $params['type'] = $file->getMimeType();
            $params['size'] = $file->getSize();
            $params['owner'] = $this->dto->code;

            $uploadPath = env('DOC_PREFIX_ENV',
                    'local') . DIRECTORY_SEPARATOR . self::SOURCE_UPLOAD . DIRECTORY_SEPARATOR . $params['owner'];
            $fileName = uniqid(time()) . '_' . $params['file_name'];
            $params['path'] = Storage::putFileAs($uploadPath, $file, $fileName);

            Document::create($params);
        }

        return $this;
    }

    protected function addNotification()
    {
        if ($this->lecturer) {
            $data = [
                'title' => '',
                'message' => "Bạn được yêu cầu hướng dẫn trong một đề tài.",
            ];
            $this->lecturer->notifications()->create($data);
            Socket::sendUpdateNotificationRequest([data_get($this->lecturer, 'id')], $data);

            $dataEmail = [
                'topic' => $this->topic,
            ];
            event(new SendEmailEvent([
                'email' => data_get($this->lecturer, 'email'),
                'email_body' => new EmailForQueuing('SINH VIÊN ĐỀ XUẤT ĐỀ TÀI', $dataEmail, 'MailLecturerProposal'),
            ]));
        }
    }
}
