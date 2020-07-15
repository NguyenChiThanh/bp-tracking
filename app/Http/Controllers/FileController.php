<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FileController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }


    public function upload(Request $request)
    {
        $request->validate(['file' => 'file|max:10000']);

        $file = $request->file('file');

        $fileName = 'channel_'.time().'_'.$file->getClientOriginalName();
        $filePath = $file->storeAs('uploads', $fileName, 'public');

        return [
            'file_path' => 'storage/'.$filePath
        ];
    }
}
