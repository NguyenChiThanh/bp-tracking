<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Requests\Channels\ChannelRequest;
use App\Models\Channel;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ChannelController extends BaseController
{
    /**
     * @var Channel
     */
    protected $channel = null;

    /***
     * ChannelController constructor.
     * @param Channel $channel
     */
    public function __construct(Channel $channel)
    {
        $this->middleware('auth:api');
        $this->channel = $channel;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $channels = $this->channel->latest()->paginate(10);

        return $this->sendResponse($channels,'Channel list');
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
     * Channel a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ChannelRequest $request)
    {
        $imageUrl = $request->get('image_url');
        if ($imageUrl) {
            try {
                Storage::move($imageUrl, storage_path('public/'.$imageUrl));
            } catch (Exception $exception) {
                Log::error($exception);
            }
        }

        $channel = $this->channel->create([
            'name' => $request->get('name'),
            'status' => $request->get('status'),
            'image_url' => $imageUrl
        ]);

        return $this->sendResponse($channel, 'Channel Created Successfully');
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
    public function update(ChannelRequest $request, $id)
    {
        $channel = $this->channel->findOrFail($id);

        $channel->update($request->all());

        // update pivot table
//        $tag_ids = [];
//        foreach ($request->get('tags') as $tag) {
//            $tag_ids[] = $tag['id'];
//        }
//        $channel->tags()->sync($tag_ids);

        return $this->sendResponse($channel, 'Channel Information has been updated');
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
        $channel = $this->channel->findOrFail($id);
        $channel->delete();

        return $this->sendResponse($channel, 'Channel has been Deleted');
    }
}
