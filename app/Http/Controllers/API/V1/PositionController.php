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
     * @param PositionRequest $request
     * @return \Illuminate\Http\Response|mixed
     */
    public function store(PositionRequest $request)
    {
        try{
            $position = $this->position->create([
                'name' => $request->get('name'),
                'description'=> $request->get('description'),
                'status' =>  $request->get('status'),
                'image_url'=> $request->get('image_url'),
                'store_id' => $request->get('store'),
                'channel'=> $request->get('channel'),
                'buffer_days' =>  $request->get('buffer_days'),
                'unit'=> $request->get('unit'),
                'price'=> $request->get('price'),
            ]);

            return $this->sendResponse($position, 'Position Created Successfully');
        } catch (Exception $e) {
            Log::error($e);
            $response = [
                'success' => false,
                'message' => $e->getMessage(),
            ];

            return response()->json($response, 200);
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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(PositionRequest $request, $id)
    {
        $position = $this->position->findOrFail($id);
        $data = [
            'name' => $request->get('name'),
            'description'=> $request->get('description'),
            'status' =>  $request->get('status'),
            'image_url'=> $request->get('image_url'),
            'store_id' => $request->get('store'),
            'channel'=> $request->get('channel'),
            'buffer_days' =>  $request->get('buffer_days'),
            'unit'=> $request->get('unit'),
            'price'=> $request->get('price'),
        ];

        $position->update($data);

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
        // $this->authorize('isAdmin');
        $position = $this->position->findOrFail($id);
        $position->delete();

        return $this->sendResponse($position, 'Position has been Deleted');
    }
}
