<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Requests\Positions\PositionRequest;
use App\Models\Position;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PositionController extends BaseController
{
    /**
     * @var Position
     */
    protected $position = null;

    /***
     * PositionController constructor.
     * @param Position $position
     */
    public function __construct(Position $position)
    {
        $this->middleware('auth:api');
        $this->position = $position;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $positions = $this->position->with('store')->latest()->paginate(10);

        return $this->sendResponse($positions,'Position list');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Position a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PositionRequest $request)
    {
        $imageUrl = $request->get('image_url');
        if ($imageUrl) {
            try {
                Storage::move($imageUrl, storage_path('public/'.$imageUrl));
            } catch (Exception $exception) {
                Log::error($exception);
            }
        }

        try {
            $position = $this->position->create([
                'name' => $request->get('name'),
                'status' => Position::ACTIVE,
                'image_url' => $imageUrl
            ]);
            return $this->sendResponse($position, 'Position Created Successfully');
        } catch (Exception $e) {
            Log::error($e);
            return $this->sendError($e->getMessage(), [], 400);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(PositionRequest $request, $id)
    {
        $position = $this->position->findOrFail($id);

        $position->update($request->all());

        return $this->sendResponse($position, 'Position Information has been updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->authorize('isAdmin');
        $position = $this->position->findOrFail($id);
        $position->delete();

        return $this->sendResponse($position, 'Position has been Deleted');
    }
}
