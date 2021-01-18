<?php

namespace App\Adapters\File;

use App\Models\Storage\File;
use Illuminate\Http\UploadedFile;
use Storage;

class FileLocalAdapter implements BaseFileAdapter
{
    public $file;

    public function __construct(File $file)
    {
        $this->file = $file;
    }

    public function getStorage()
    {
        return Storage::disk(File::DISK_LOCAL);
    }

    public function storeFile(UploadedFile $file, string $destination): string
    {
        $file_name = $file->hashName();
        $file->storeAs(rtrim($destination, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR, $file_name, [
            'disk' => File::DISK_LOCAL,
        ]);

        return $file_name;
    }

    public function removeFileFromStorage(): bool
    {
        return $this->getStorage()->delete($this->getRelativeStoragePath());
    }

    public function getFullUrl(): string
    {
        return $this->getStorage()->url(rtrim($this->file->disk_path, '/').'/'.$this->file->file);
    }

    public function getRelativeStoragePath(): string
    {
        return rtrim($this->file->disk_path, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.$this->file->file;
    }

    public function getAbsoluteStoragePath(): string
    {
        return $this->getStorage()->path(rtrim($this->file->disk_path,
                DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.$this->file->file);
    }

    public function getDownloadUrl(): string
    {
        return $this->getAbsoluteStoragePath();
    }
}
