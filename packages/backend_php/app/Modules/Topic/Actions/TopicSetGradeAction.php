<?php

namespace App\Modules\Topic\Actions;

use App\Entities\Grade;
use App\Entities\Topic;
use App\Exceptions\UserException;
use App\Modules\Topic\DTO\TopicSetGradeDTO;
use Illuminate\Support\Facades\Auth;

class TopicSetGradeAction
{
    /**
     * @var TopicSetGradeDTO
     */
    public $dto;
    public $types;
    public $topic;
    public $students;
    public $schedule;
    public $details;
    public $studentIds;
    public $criteriaIds;

    /**
     * @param TopicSetGradeDTO $dto
     */
    public function handle($dto)
    {
        $this->types = array_keys(Grade::type());
        $this->dto = $dto;

        $this->validateData()
            ->syncScore()
            ->calculateMark();
    }

    protected function validateData()
    {
        $this->topic = Topic::find($this->dto->id);
        if (!$this->topic) {
//            throw new UserException("Topic not found!");
            throw new UserException('Đề tài không tồn tại trong hệ thống!', 400);
        }

        $this->students = $this->topic->students;
        if (!$this->students) {
//            throw new UserException("Topic does not have students!");
            throw new UserException('Đề tài không có sinh viên!', 400);
        }

        $this->schedule = $this->topic->schedule;
        if (!$this->schedule) {
//            throw new UserException("Schedule not found!");
            throw new UserException('Đợt đăng ký không tồn tại trong hệ thống!', 400);
        }

        $this->details = $this->dto->details;
        $this->studentIds = array_unique(array_column($this->details, 'student_id'));
        $this->criteriaIds = array_unique(array_column($this->details, 'criteria_id'));

        return $this;
    }

    protected function syncScore()
    {
        $this->topic->grade()
            ->where('type', $this->dto->type)
            ->whereIn('criteria_id', $this->criteriaIds)
            ->whereIn('student_id', $this->studentIds)
            ->delete();

        foreach ($this->details as $detail) {
            $this->topic->grade()
                ->create([
                    'type' => $this->dto->type,
                    'criteria_id' => data_get($detail, 'criteria_id'),
                    'student_id' => data_get($detail, 'student_id'),
                    'score' => data_get($detail, 'score'),
                    'graded_by' => Auth::id(),
                ]);
        }

        return $this;
    }

    protected function calculateMark()
    {
        $grades = $this->topic->grade()
            ->where('type', $this->dto->type)
            ->get();


        $studentCount = $grades->unique('student_id')->count();
        $criteria = $this->schedule->scheduleCriteria;
        $sum = 0;
        foreach ($this->details as $detail) {
            $dtlCriteria = $criteria->where('criteria_id', data_get($detail, 'criteria_id'))->first();
            $scoreRate = data_get($dtlCriteria, 'score_rate', 0);
            $sum += data_get($detail, 'score', 0) * $scoreRate / 100;
        }

        $average = $sum / $studentCount;

        switch ($this->dto->type) {
            case Grade::TYPE_LECTURER:
                $this->topic->update([
                    'lecturer_grade' => $average,
                ]);
                break;
            case Grade::TYPE_CRITICAL:
                $this->topic->update([
                    'critical_grade' => $average,
                ]);
                break;
            case Grade::TYPE_PRESIDENT:
                $this->topic->update([
                    'committee_president_grade' => $average,
                ]);
                break;
            case Grade::TYPE_SECRETARY:
                $this->topic->update([
                    'committee_secretary_grade' => $average,
                ]);
                break;
            default:
                break;
        }

        return $this;
    }
}