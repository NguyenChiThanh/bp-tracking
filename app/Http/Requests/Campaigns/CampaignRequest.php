<?php

namespace App\Http\Requests\Campaigns;

use App\Constraints\CampaignStatusConstraint;
use Carbon\Carbon;
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
            'brand.id' => 'required|integer',
            'status.value' => [
                'required',
                'in:' . implode(',', CampaignStatusConstraint::getAll(true)),
                function ($attribute, $value, $fail) {
                    $from_ts = $this->get('from_ts');

                    if ($from_ts && $value) {
                        $from_ts = Carbon::createFromTimestamp($from_ts)->setTime(0, 0, 0);
                        if ($from_ts->isAfter(Carbon::now()) && $value == CampaignStatusConstraint::STATUS_RESERVED) {
                            $fail('The Status is not valid with From Date');
                        }
                    }

                    return true;
                },
            ],
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

    public function attributes()
    {
        return [
            'name' => 'Campaign Name',
            'from_ts' => 'From Date',
            'to_ts' => 'To Date',
            'brand.id' => 'Brand',
            'status.value' => 'Status',
        ];
    }
}
