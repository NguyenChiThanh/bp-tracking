<?php


namespace App\Services;

use App\Models\Storage\File;
use Exception;
use DB;
use Illuminate\Http\UploadedFile;

class FileService
{
    public function __construct()
    {
    }

    public function createFile($params = [], UploadedFile $uploadedFile = null)
    {
        try {
            $disk = ! empty($params['disk']) ? $params['disk'] : config('filesystems.default');
            // Need disk and url path because it maybe not the same (DIRECTORY_SEPARATOR)
            $disk_path = ! empty($params['disk_path']) ? $params['disk_path'] : 'temporary';

            DB::beginTransaction();
            $newFile = new File($params);

            // Store file
            if (empty($params['file']) && $uploadedFile) {
                $newFile->file = $newFile->getAdapter()->storeFile($uploadedFile, $disk_path);
            }
            $newFile->save();
            DB::commit();

            return $newFile;
        } catch (Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
    }

    /**
     * Download file.
     */
    public function download($fileID)
    {
        /**
         * @var File
         */
        $file = File::query()->findOrFail($fileID, ['disk', 'disk_path', 'file', 'name']);
        $path = $file->getAdapter()->getDownloadUrl();

        return response()->download($path, $file->name);
    }
}
