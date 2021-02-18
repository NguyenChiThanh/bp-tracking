<?php


namespace App\Http\Controllers\API\V1;


use App\Http\Requests\Utlities\ImportRequest;
use App\Imports\Campaign\CampaignImport;
use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Exception;

class UtilityController extends BaseController
{
    public function index(Request $request)
    {
        return $this->sendResponse([
            'items' => Utility::query()->latest('id')->get(),
        ], 'Utilities List');
    }

    public function import(ImportRequest $request)
    {
        $excel_file = $request->file('file');

//        $validator = Validator::make($request->all(), [
//            'file' => 'required',
//            'type' => 'required',
//        ]);
//
//        $validator->after(function ($validator) use ($excel_file) {
//            if (!in_array($excel_file->guessClientExtension(), ['xlsx', 'xls'])) {
//                return $this->sendError('File type is invalid - only xlsx is allowed');
//            }
//
//            return true;
//        });

        $type = $request->get('type');

        try {
            $fname = md5(rand()).'.xlsx';
            Storage::disk('public')->put('files'.DIRECTORY_SEPARATOR.$fname, \File::get($excel_file));
            $utility = Utility::query()->firstOrNew(['file_name' => $fname]);
            $utility->imported = Utility::IMPORT_PENDING; //file was not imported
            $utility->type = $type;
            $utility->user_id = auth()->user()->id;
            $utility->name = $excel_file->getClientOriginalName();
            $utility->current_step = 'Importing';
            $utility->language = $request->server('HTTP_ACCEPT_LANGUAGE', 'vi');
            $utility->save();

            // IMPORT USING QUEUE
//            (new CampaignImport)($utility);
            Artisan::call('utility:import', ['utility_id' => $utility->getKey()]);


            return $this->sendResponse($utility, 'Done');
        } catch (Exception $e) {
            Log::error($e);

            return $this->sendError($e->getMessage());
        }
    }
}
