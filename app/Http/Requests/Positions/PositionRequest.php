<?php


namespace App\Http\Requests\Positions;


use Illuminate\Foundation\Http\FormRequest;

class PositionRequest extends FormRequest
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
            'description' => 'nullable|string',
            'status' => 'nullable|string',
            'image_url' => 'nullable|string',
            'store.id' => 'required|integer',
            'channel' => 'required|string',
            'buffer_days' => 'required|integer|gt:0',
            'unit' => 'required|string',
            'price' => 'required|numeric|gt:0',
        ];
    }
}
