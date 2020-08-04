<?php

namespace App\Http\Requests\Campaigns;

use Illuminate\Foundation\Http\FormRequest;

class CampaignRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string|min:5',
            'contract_code' => 'nullable|string',
            'license_code' => 'nullable|string',
            'brand_id' => 'required|integer',
            'position_list' => 'required|array',
            'from_ts' => 'required|integer',
            'to_ts' => 'required|integer',
            'discount_type' => 'required|string',
            'discount_value' => ['required','regex:/^\d+\.\d+|\d+$/'],
//            'discount_max' => 'required|regex:/^\d*(\.\d{2})?$/',
            'total_discount' => 'required|regex:/^\d*(\.\d{2})?$/',
            'total_price' => 'required|regex:/^\d*(\.\d{2})?$/',
            'days_diff' => 'required|integer',
            'position_price' => 'required|regex:/^\d*(\.\d{2})?$/',
        ];
    }
}
