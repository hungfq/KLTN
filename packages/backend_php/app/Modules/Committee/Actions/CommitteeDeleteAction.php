<?php

namespace App\Modules\Committee\Actions;

use App\Entities\Committee;
use App\Exceptions\UserException;

class CommitteeDeleteAction
{
    public $id;
    public $committee;

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
        $this->committee = Committee::find($this->id);
        if (!$this->committee) {
//            throw new UserException("Committee not found!");
            throw new UserException("Hội đồng không ồn tại!", 400);
        }

        return $this;
    }

    protected function delete()
    {
        $this->committee->delete();
        return $this;
    }
}
