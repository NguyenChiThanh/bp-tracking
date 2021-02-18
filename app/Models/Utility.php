<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Log;

class Utility extends Model
{
    protected $table = 'utilities';

    protected $fillable = [
        'file_name',
        'imported',
        'type',
        'app',
        'rows_imported',
        'total_rows'
    ];

    protected $casts = [
        'params' => 'array',
        'result' => 'array',
        'created_at' => 'timestamp',
    ];

    protected $appends = ['type_name'];

    const IMPORT_PENDING = 0;
    const IMPORT_PROCESSING = 1;
    const IMPORT_SUCCESS = 2;
    const IMPORT_FAIL = 9;

    const IMPORT_MODE_CREATE = 'create';
    const IMPORT_MODE_UPDATE = 'update';

    public function getTempStoreUrl($full = true) {
        $folderPath = 'app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR;
        return storage_path($folderPath) . ($full ? $this->file_name : '');
    }

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function initializeImportInProgress($total) {
        $this->total_rows = $total;
        $this->rows_imported = 0;
        $this->current_step = trans('messages.common.importing');
        $this->imported = Utility::IMPORT_PROCESSING;
        $this->save();
    }

    public function updateProgress(int $newRowImported) {
        $this->fresh(); //reload from the database
        $this->rows_imported = $newRowImported;
        $this->save();
    }

    public function finalizeImportProcess($result, $importedRows) {
        $this->current_step = trans('messages.common.completed');
        $this->imported = Utility::IMPORT_SUCCESS;
        $this->rows_imported = $importedRows;
        $this->result = $result;
        $this->save();
    }

    public function finalizeProcess($data)
    {
        $this->current_step = trans('messages.common.completed');
        $this->imported = Utility::IMPORT_SUCCESS;

        foreach ($data as $key => $datum) {
            $this->{$key} = $datum;
        }
        $this->save();
    }

    /**
     * $exception can be Throwable or Exception
     */
    public function errorImport($exception) {
        Log::error($exception);
        $this->imported = Utility::IMPORT_FAIL;
        $this->message = $exception->getMessage();
        $this->save();
    }

    public function updateCurrentStep($current_step)
    {
        $this->current_step = $current_step;
        $this->save();
    }

    public function getTypeNameAttribute()
    {
        return ucwords(str_replace('_', ' ', $this->type));
    }
}
