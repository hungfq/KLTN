<?php

namespace App\Modules\Committee\Actions;


use App\Entities\Committee;
use App\Entities\Topic;
use App\Exceptions\UserException;
use App\Libraries\ImportHelpers;
use App\Modules\Committee\DTO\CommitteeImportTopicDTO;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;

class CommitteeImportTopicAction
{
    /**
     * @param $dto CommitteeImportTopicDTO
     * @throws UserException
     */
    public function handle($dto)
    {
        $committee = Committee::find($dto->id);
        if (!$committee) {
//            throw new UserException("Committee is not exists!");
            throw new UserException('Hội đồng không tồn tại!', 400);
        }
        $topicIds = [];

        $file = $dto->file;
        $extFile = $file->extension();
        $arrFile = Excel::toArray(new Collection(), $file)[0];

        $heading = [
            'code' => 'Mã đề tài(*)',
            'title' => 'Tiêu đề(*)',
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
            'title',
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

            if ($arrFile->where('code', $input['code'])
                    ->count() > 1) {
                $error[] = str_replace('{0}', $heading['code'], $msgDuplicate);
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

                $topic = null;
                if (!$topic = Topic::where('code', $input['code'])->first()) {
//                    throw new UserException("Topic is not exists!");
                    throw new UserException(sprintf('Đề tài %s - %s không tồn tại!',
                        data_get($input, 'code', ''),
                        data_get($input, 'title', '')
                    ), 400);
                }

                $topic->update([
                    'committee_id' => data_get($committee, 'id'),
                ]);
            }
        }


        if ($arrFile = $arrFile->toArray()) {
            $arrFile[0] = array_combine(
                    array_keys($heading),
                    $headingFile
                ) + ['error' => $errorColumn];

            ksort($arrFile);

            ob_end_clean();
            return (new Collection($arrFile))->downloadExcel("TopicCommitteeImportError.{$extFile}");
        }

        return true;
    }
}