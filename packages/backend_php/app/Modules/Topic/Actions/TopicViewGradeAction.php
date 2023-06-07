<?php

namespace App\Modules\Topic\Actions;

use App\Entities\ScheduleCriteria;
use App\Entities\Topic;
use App\Exceptions\UserException;
use App\Modules\Topic\DTO\TopicViewGradeDTO;

class TopicViewGradeAction
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
                'schedule_criteria.*',
                'criteria.title',
                'criteria.description',
                'topic_students.student_id',
//                'grades.student_id',
                'grades.type',
                'grades.score',
                'grades.graded_by',
            ])
            ->join('criteria', function ($q) {
                $q->on('schedule_criteria.criteria_id', '=', 'criteria.id')
                    ->where('criteria.deleted', 0);
            })
            ->join('topics', function ($q) {
                $q->on('topics.schedule_id', '=', 'schedule_criteria.schedule_id')
                    ->where('topics.id', '=', $this->topic->id)
                    ->where('topics.deleted', 0);
            })
            ->join('topic_students', 'topic_students.topic_id', '=', 'topics.id')
            ->leftJoin('grades', function ($q) use ($dto) {
                $q->on('grades.topic_id', '=', 'topics.id')
                    ->on('grades.criteria_id', '=', 'schedule_criteria.criteria_id')
                    ->on('grades.student_id', '=', 'topic_students.student_id')
                    ->where('grades.deleted', 0);

                if ($type = $dto->type) {
                    $q->where('grades.type', $type);
                }
            })
            ->where('schedule_criteria.schedule_id', $this->topic->schedule_id);

        if ($dto->student_id) {
            $query->where('topic_students.student_id', $dto->student_id);
        }

        $query->addSelect([
            'st.name as student_name',
            'gb.name as graded_by_name',
        ]);

        $query->leftJoin('users as st', 'st.id', '=', 'topic_students.student_id')
            ->leftJoin('users as gb', 'gb.id', '=', 'grades.graded_by');

        return $query->get();
    }
}