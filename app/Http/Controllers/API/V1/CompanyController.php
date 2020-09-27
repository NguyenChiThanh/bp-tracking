<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\API\V1\BaseController;
use App\Models\Brand;
use App\Models\Company;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CompanyController extends BaseController
{
    /***
     * @var Company
     */
    protected $company;

    /**
     * @var Brand
     */
    protected $brand;

    public function __construct(Company $company, Brand $brand)
    {
        $this->company = $company;
        $this->brand = $brand;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function list(Request $request)
    {
        $companies = [];
        if($request->get('all')) {
            $companies = $this->company->with('brands')->latest()->get();
        } else {
            $companies = $this->company->with('brands')->latest()->paginate(10);
        }

        return $this->sendResponse($companies, 'Company list');
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
    public function store(Request $request)
    {
        try {
            $name = $request->get('name');

            $company = $this->company->create(['name' => $name]);

            $brands = $request->get('brands');

            foreach ($brands as $b) {
                $brand = $this->brand->findOrFail($b['id']);
                $brand->update(['company_id' => $company->id]);
            }

            return $this->sendResponse($company, 'Company Created Successfully');
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
    public function update(Request $request, $id)
    {
        $company = $this->company->findOrFail($id);

        // unleash old brands
        foreach ($company->brands as $b) {
            $b->update(['company_id' => null]);
        }

        // set new company_id for brand
        $brands = $request->get('brands');
        foreach ($brands as $b) {
            $brand = $this->brand->findOrFail($b['id']);
            $brand->update(['company_id' => $company->id]);
        }

        $name = $request->get('name');
        $company->update(['name' => $name]);

        return $this->sendResponse($company, 'Company Information has been updated');
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
        $company = $this->company->findOrFail($id);

        // set brand.company_id = null
        $brands = $this->brand->where('company_id', $company->id)->get();
        foreach ($brands as $brand) {
            $brand->update(['company_id' => null]);
        }
        $company->delete();

        return $this->sendResponse($company, 'Company has been Deleted');
    }
}
