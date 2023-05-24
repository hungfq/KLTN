<?php

namespace App\Modules\Topic\Actions;

use App\Entities\Topic;
use App\Exceptions\UserException;
use App\Modules\Topic\DTO\TopicSetGradeDTO;

class TopicSetGradeAction
{
    /**
     * @var TopicSetGradeDTO
     */
    public $dto;
    public $topic;
    public $details;

    /**
     * @param TopicSetGradeDTO $dto
     */
    public function handle($dto)
    {
        $this->dto = $dto;

        $this->validateData()
            ->syncScore()
            ->calculateMark();
    }

    protected function validateData()
    {
        $this->topic = Topic::find($this->dto->id);
        if (!$this->topic) {
            throw new UserException("Topic not found!");
        }

        $this->details = $this->dto->details;

        return $this;
    }

    protected function syncScore()
    {

        return $this;
    }

    protected function calculateMark()
    {

        return $this;
    }
}