<?php

namespace App\Modules\Schedule\Actions;


use App\Entities\Role;
use App\Entities\Schedule;
use App\Entities\User;
use App\Exceptions\UserException;
use App\Libraries\ImportHelpers;
use App\Modules\Schedule\DTO\ScheduleImportStudentDTO;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;

class ScheduleImportStudentAction
{
    /**
     * @param $dto ScheduleImportStudentDTO
     * @throws UserException
     */
    public function handle($dto)
    {
        $schedule = Schedule::find($dto->id);
        if (!$schedule) {
//            throw new UserException("Schedule is not exists!");
            throw new UserException('Đợt đăng ký không tồn tại!', 400);
        }
        $studentIds = [];

        $role = Role::where('name', Role::ROLE_STUDENT)->first();
        if (!$role) {
//            throw new UserException("Role is not exists!");
            throw new UserException('Vai trò không tồn tại!', 400);
        }

        $file = $dto->file;
        $extFile = $file->extension();
        $arrFile = Excel::toArray(new Collection(), $file)[0];

        $heading = [
            'code' => 'Mã(*)',
            'name' => 'Họ và Tên(*)',
            'email' => 'Email(*)',
            'gender' => 'Giới tính',
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
            'code',
            'name',
            'email',
        ];

        $arrValidMaxLen = [];
        $arrValidNumber = [];
        $arrValidMinNumber = [];

        $msgEmpty = (ImportHelpers::MSG_EMPTY);
        $msgMaxLength = (ImportHelpers::MSG_MAX_LENGTH);
        $msgDuplicate = (ImportHelpers::MSG_DUPLICATE);
        $msgNotNumber = (ImportHelpers::MSG_NOT_NUMBER);
        $msgMinNumber = (ImportHelpers::MSG_MIN_NUMBER);
        $errorColumn = (ImportHelpers::ERROR_COLUMN);

//        $users = User::all();
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

            if ($arrFile->where('email', $input['email'])
                    ->count() > 1) {
                $error[] = str_replace('{0}', 'User', $msgDuplicate);
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

                $user = null;
                if (!$user = User::role(Role::ROLE_STUDENT)->where('email', $input['email'])->first()) {
                    $user = User::create([
                        "name" => data_get($input, 'name'),
                        "code" => data_get($input, 'code'),
                        "email" => data_get($input, 'email'),
                        "gender" => strtoupper(data_get($input, 'gender')) == "NAM" ? "male" : "female",
                        "status" => User::STATUS_ACTIVE,
                    ]);
                    $user->roles()->attach($role->id);
                }

                $studentIds[] = data_get($user, 'id');
            }
        }

        $schedule->students()->sync($studentIds);

        if ($arrFile = $arrFile->toArray()) {
            $arrFile[0] = array_combine(
                    array_keys($heading),
                    $headingFile
                ) + ['error' => $errorColumn];

            ksort($arrFile);

            ob_end_clean();
            return (new Collection($arrFile))->downloadExcel("UserImportError.{$extFile}");
        }

        return true;
    }
}