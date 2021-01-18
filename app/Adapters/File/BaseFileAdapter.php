<?php

namespace App\Adapters\File;

use Illuminate\Http\UploadedFile;

interface BaseFileAdapter
{
    public function getStorage();

    public function storeFile(UploadedFile $file, string $destination) : string;

    public function removeFileFromStorage() : bool;

    public function getFullUrl() : string;

    // Storage
    public function getRelativeStoragePath() : string;

    public function getAbsoluteStoragePath() : string;

    public function getDownloadUrl() : string;
}
