<?php

namespace App\Modules\Topic\Actions;

use App\Entities\Role;
use App\Entities\Schedule;
use App\Entities\Topic;
use App\Entities\User;
use App\Exceptions\UserException;
use App\Libraries\ImportHelpers;
use App\Modules\Topic\DTO\TopicImportDTO;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;

class TopicImportAction
{
    /**
     * @param $dto TopicImportDTO
     * @throws UserException
     */
    public function handle($dto)
    {
        $file = $dto->file;
        $extFile = $file->extension();
        $arrFile = Excel::toArray(new Collection(), $file)[0];

        $heading = [
//            'code' => 'Mã đề tài(*)',
            'title' => 'Tiêu đề(*)',
            'limit' => 'Số lượng SV(*)',
//            'thesis_defense_date' => 'Thesis Defense Date',
            'schedule_code' => 'Mã đợt(*)',
            'lecturer_code' => 'Mã GVHD',
            'critical_code' => 'Mã GVPB',
            'description' => 'Mô tả',
        ];

        $headingFile = $arrFile[0];
        unset($arrFile[0]);

        $total = count($arrFile);

        if ($total < 1) {
//            throw new UserException(('File is empty'));
            throw new UserException('Tệp rỗng!', 400);
        }

        if (count($headingFile) != count($heading)) {
//            throw new UserException(('The format of imported file is invalid.'));
            throw new UserException('Định dạng tệp không hợp lệ!', 400);
        }

        if ($total > 1000) {
//            throw new UserException('The maximum rows of file is {0}', 1000);
            throw new UserException(sprintf('Số lượng dòng tối đa là %d', 1000), 400);
        }

        foreach ($arrFile as $index => $row) {
            if (ImportHelpers::checkRowEmpty($row)) {
                unset($arrFile[$index]);
                continue;
            }

            $input = array_combine(
                array_keys($heading),
                $row
            );

            $arrFile[$index] = array_map('trim', $input);
        }

        if (empty($arrFile)) {
//            throw new UserException(('File is empty'));
            throw new UserException('Tệp rỗng!', 400);
        }

        $arrValidRequire = [
//            'code',
            'title',
            'limit',
            'schedule_code',
        ];

        $arrValidMaxLen = [];
        $arrValidNumber = [
            'limit'
        ];
        $arrValidMinNumber = [
            'limit'
        ];

        $arrValidDate = [];

        $msgEmpty = (ImportHelpers::MSG_EMPTY);
        $msgNotExist = (ImportHelpers::MSG_NOT_EXIST);
        $msgMaxLength = (ImportHelpers::MSG_MAX_LENGTH);
        $msgDuplicate = (ImportHelpers::MSG_DUPLICATE);
        $msgNotNumber = (ImportHelpers::MSG_NOT_NUMBER);
        $msgMinNumber = (ImportHelpers::MSG_MIN_NUMBER);
        $msgDateFormat = (ImportHelpers::MSG_DATE_FORMAT);
        $errorColumn = (ImportHelpers::ERROR_COLUMN);

        $lecturers = User::role(Role::ROLE_LECTURER)->get();
//        $topics = Topic::all();
        $schedules = Schedule::all();

        $arrFile = collect($arrFile);

        foreach ($arrFile as $index => $input) {
            $error = [];

            foreach ($arrValidRequire as $item) {
                if (strlen($input[$item]) === 0) {
                    $error[] = str_replace('{0}', $heading[$item], $msgEmpty);
                }
            }

            foreach ($arrValidMaxLen as $item => $len) {
                if (strlen($input[$item]) > $len) {
                    $error[] = str_replace(['{0}', '{1}'], [$heading[$item], $len], $msgMaxLength);
                }
            }

            foreach ($arrValidNumber as $item) {
                if ($input[$item] && !is_numeric($input[$item])) {
                    $error[] = str_replace('{0}', $heading[$item], $msgNotNumber);
                }
            }

            foreach ($arrValidMinNumber as $item) {
                if (strlen($input[$item]) > 0 && is_numeric($input[$item]) && $input[$item] <= 0) {
                    $error[] = str_replace('{0}', $heading[$item], $msgMinNumber);
                }
            }

            foreach ($arrValidDate as $item) {
                if (!empty($input[$item])) {
                    if (is_numeric($input[$item])) {
                        $input[$item] = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($input[$item])->format('Y-m-d');
                        $arrFile[$index][$item] = $input[$item];
                    }

                    if (!ImportHelpers::isDate($input[$item])) {
                        $error[] = str_replace(['{0}', '{1}', '{2}'], [$heading[$item], '(yyyy-mm-dd)', '(mm/dd/yyyy)'], $msgDateFormat);
                    }
                }
            }

//            if ($arrFile->where('code', $input['code'])
//                    ->count() > 1) {
//                $error[] = str_replace('{0}', $heading['code'], $msgDuplicate);
//            }
//
//            if ($topics->where('code', $input['code'])
//                ->first()) {
//                $error[] = str_replace('{0}', $heading['code'], $msgDuplicate);
//            }

            $schedule = null;
            if ($input['schedule_code']) {
                $schedule = $schedules->where('code', $input['schedule_code'])->first();
                if (!$schedule) {
                    $error[] = str_replace(['{0}'], [$heading['schedule_code']], $msgNotExist);
                }
            }

            $lecturer = null;
            if ($input['lecturer_code']) {
                $lecturer = $lecturers->where('code', $input['lecturer_code'])->first();
                if (!$lecturer) {
                    $error[] = str_replace(['{0}'], [$heading['lecturer_code']], $msgNotExist);
                }
            }

            $critical = null;
            if ($input['critical_code']) {
                $critical = $lecturers->where('code', $input['lecturer_code'])->first();
                if (!$critical) {
                    $error[] = str_replace(['{0}'], [$heading['critical_code']], $msgNotExist);
                }
            }

            if ($error) {
                $newRow = $arrFile->get($index);
                $newRow['error'] = implode(', ', $error);
                $arrFile[$index] = $newRow;

                $arrFile[$index] = array_combine(
                    array_keys($heading) + ['error' => $errorColumn],
                    $arrFile[$index]
                );
            } else {
                unset($arrFile[$index]);

                $importTopic = [
                    "code" => Topic::generateTopicCode($schedule),
                    "title" => data_get($input, 'title'),
                    "description" => data_get($input, 'description'),
                    "limit" => data_get($input, 'limit'),
//                    "thesis_defense_date" => $input['thesis_defense_date'] ?: null,
                    "schedule_id" => data_get($schedule, 'id'),
                    "lecturer_id" => data_get($lecturer, 'id'),
                    "critical_id" => data_get($critical, 'id'),
                ];

                Topic::create($importTopic);
            }
        }

        if ($arrFile = $arrFile->toArray()) {
            $arrFile[0] = array_combine(
                    array_keys($heading),
                    $headingFile
                ) + ['error' => $errorColumn];

            ksort($arrFile);

            ob_end_clean();
            return (new Collection($arrFile))->downloadExcel("TopicImportError.{$extFile}");
        }

        return true;
    }
}