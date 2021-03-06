<?php

namespace App\Http\Controllers;

use App\Services\FileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FileController extends Controller
{
    protected $fileService;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(FileService $fileService)
    {
        $this->middleware('auth');
        $this->fileService = $fileService;
    }


    public function upload(Request $request)
    {
        $request->validate(['file' => 'file|max:10000']);

        $file = $request->file('file');

        if ($request->get('type') && $request->get('type') == 'positions') {
            $fileName = 'import_positions_'.time().'_'.$file->getClientOriginalName();
            $filePath = $file->storeAs('uploads', $fileName);
            return [
                'file_path' => $filePath
            ];
        }

        $fileName = 'channel_'.time().'_'.$file->getClientOriginalName();
        $filePath = $file->storeAs('uploads', $fileName, 'public');

        return [
            'file_path' => 'storage/'.$filePath
        ];
    }

    public function download($id)
    {
        return $this->fileService->download($id);
    }
}
