<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Requests\Stores\StoreRequest;
use App\Models\Store;
use Illuminate\Http\Request;

class StoreController extends BaseController
{
    /**
     * @var Store
     */
    protected $store = null;

    /***
     * StoreController constructor.
     * @param Store $store
     */
    public function __construct(Store $store)
    {
        $this->middleware('auth:api');
        $this->store = $store;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $stores = $this->store->latest()->paginate(10);

        return $this->sendResponse($stores,'Store list');
    }

    public function list(Request $request)
    {
        $wardId = $request->input('ward_id');
        $stores = $this->store->all();
        if($wardId) {
            $stores = $this->store->where('ward_id', $wardId)->get();
        }
        $data = [];
        foreach ($stores as $store) {
            $data[] = [
                'id' => $store->id,
                'name' => $store->name . ', ' . $store->ward . ', ' . $store->district . ', ' . $store->province
            ];
        }
        return $this->sendResponse(['data' => $data], 'Store list');
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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request)
    {
        $store = $this->store->create([
            'name' => $request->get('name'),
            'description' => $request->get('description'),
            'status' => $request->get('status'),
        ]);

        return $this->sendResponse($store, 'Store Created Successfully');
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
    public function update(StoreRequest $request, $id)
    {
        $store = $this->store->findOrFail($id);

        $store->update($request->all());

        // update pivot table
//        $tag_ids = [];
//        foreach ($request->get('tags') as $tag) {
//            $tag_ids[] = $tag['id'];
//        }
//        $store->tags()->sync($tag_ids);

        return $this->sendResponse($store, 'Store Information has been updated');
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
        $store = $this->store->findOrFail($id);
        $store->delete();

        return $this->sendResponse($store, 'Store has been Deleted');
    }
}
