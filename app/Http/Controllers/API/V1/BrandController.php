<?php

namespace App\Http\Controllers\API\V1;

use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BrandController extends BaseController
{
    /**
     * @var Brand
     */
    protected $brand;

    public function __construct(Brand $brand)
    {
        $this->brand = $brand;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function list(Request $request)
    {
        // get brands
        $brands = $this->brand->with('company')->get();

        $companyId = $request->get("company_id");
        if ($companyId) {
            $brands = $this->brand->where('company_id', $companyId)->with('company')->get();
        }

        $brandIds = $request->get('brand_ids');
        if ($brandIds) {
            $brands = $this->brand->whereIn('id', explode(',', $brandIds))->with('company')->get();
        }

        return $this->sendResponse($brands, 'Brand list');
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
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $name = $request->get('name');
            $company = $request->get('company');

            $this->brand->create([
                'name' => $name,
                'company_id' => $company['id'],
            ]);

            return $this->sendResponse($company, 'Brand Created Successfully');
        } catch (Exception $e) {
            Log::error($e);
            return $this->sendError($e->getMessage(), [], 400);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $brand = $this->brand->findOrFail($id);
            $name = $request->get('name');
            $company = $request->get('company');
            $data = [
                'name' => $name,
                'company_id' => $company['id'],
            ];

            $brand->update($data);

        } catch (Exception $e) {
            Log::error($e);
            return $this->sendError($e->getMessage(), [], 400);
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // $this->authorize('isAdmin');
        $brand = $this->brand->findOrFail($id);
        $brand->delete();

        return $this->sendResponse($brand, 'Brand has been Deleted');
    }
}
