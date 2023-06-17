<?php

namespace App\Modules\Documents\Repositories;

use App\Entities\Document;
use App\Modules\Documents\Validators\CreateDocumentValidator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

class DocumentRepo
{
    const SOURCE_UPLOAD = 'documents';

    /**
     * @return string
     */
    public function model()
    {
        return Document::class;
    }

    public function search($input = [])
    {
        $query = Document::query()
            ->select([
                'documents.*',
            ]);

        $this->searchFields($query, $input);

        return $query->paginate($input['limit'] ?? 10);
    }

    /**
     * @param $query
     * @param $input
     */
    private function searchFields(\Illuminate\Database\Eloquent\Builder &$query, $input)
    {
        $query->when(array_get($input, 'owner'), function ($query, $owner) {
            $query->where('owner', $owner);
        });

        $query->when(array_get($input, 'title'), function ($query, $value) {
            $query->where('title', 'like', '%' . $value . '%');
        });

        $query->when(array_get($input, 'file_name'), function ($query, $value) {
            $query->where('file_name', 'like', '%' . $value . '%');
        });

        $query->when(array_get($input, 'file_extension'), function ($query, $value) {
            $query->where('file_extension', 'like', '%' . $value . '%');
        });
    }

    public function createDocument($params)
    {
        $params['file_name'] = $params['file']->getClientOriginalName();
        $params['file_extension'] = $params['file']->getClientOriginalExtension();
        $params['owner'] = Arr::get($params, 'owner');
        $pathFile = $this->uploadFile($params);
        $params['path'] = $pathFile;

        return Document::create($params);

    }

    public function validateCreateDocument(array $params)
    {
        $validator = new CreateDocumentValidator();
        $validator->validate($params);
    }

    public function uploadFile($params)
    {
        $pathFile = $this->getPathFile($params);
        $generateFileName = $this->generateFileNameUpload($params['file']);
        Storage::putFileAs($pathFile, $params['file'], $generateFileName);

        return $pathFile . DIRECTORY_SEPARATOR . $generateFileName;
    }

    private function generateFileNameUpload($file)
    {
        return uniqid(time()) . '_' . $file->getClientOriginalName();
    }

    private function getPathFile($params)
    {
        //exp: documents/KL1-001/file.txt
        return env('DOC_PREFIX_ENV', 'local') .
            DIRECTORY_SEPARATOR . self::SOURCE_UPLOAD . DIRECTORY_SEPARATOR . $params['owner'];

    }

    public function deleteDocument(array $params)
    {
        if (!$params) {
            return false;
        }

        return Document::whereIn('id', $params)->delete();
    }
}
