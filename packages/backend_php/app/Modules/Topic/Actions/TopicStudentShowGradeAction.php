<?php

namespace App\Modules\Topic\Actions;

use App\Entities\ScheduleCriteria;
use App\Entities\Topic;
use App\Exceptions\UserException;
use App\Modules\Topic\DTO\TopicViewGradeDTO;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TopicStudentShowGradeAction
{
    public $topic;

    /**
     * @param TopicViewGradeDTO $dto
     */
    public function handle($dto)
    {
        $this->topic = Topic::find($dto->id);
        if (!$this->topic) {
//            throw new UserException("Topic not found!");
            throw new UserException('Đề tài không tồn tại trong hệ thống!', 400);
        }

        $query = ScheduleCriteria::query()
            ->select([
                'schedule_criteria.criteria_id',
                'criteria.title',
                'criteria.description',
                DB::raw('SUM(CASE WHEN grades.type = "LT" THEN (grades.score) ELSE 0 END) as lecturer_grade'),
                DB::raw('SUM(CASE WHEN grades.type = "CR" THEN (grades.score) ELSE 0 END) as critical_grade'),
                DB::raw('SUM(CASE WHEN grades.type = "PD" THEN (grades.score) ELSE 0 END) as president_grade'),
                DB::raw('SUM(CASE WHEN grades.type = "SE" THEN (grades.score) ELSE 0 END) as secretary_grade'),
            ])
            ->join('criteria', function ($q) {
                $q->on('schedule_criteria.criteria_id', '=', 'criteria.id')
                    ->where('criteria.deleted', 0);
            })
            ->join('topics', function ($q) {
                $q->on('topics.schedule_id', '=', 'schedule_criteria.schedule_id')
                    ->where('topics.deleted', 0);
            })
            ->leftJoin('grades', function ($q) use ($dto) {
                $q->on('grades.criteria_id', '=', 'schedule_criteria.criteria_id')
                    ->on('grades.topic_id', '=', 'topics.id')
                    ->where('grades.deleted', 0);
            })
            ->where('schedule_criteria.schedule_id', $this->topic->schedule_id)
            ->where('topics.id', '=', $this->topic->id)
            ->where('grades.topic_id', '=', $this->topic->id)
            ->where('grades.student_id', Auth::id())
            ->groupBy('schedule_criteria.criteria_id');


        return $query->get();
    }
}