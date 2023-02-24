<?php

namespace App\Http\Requests\Waybill;

use Illuminate\Foundation\Http\FormRequest;

class WaybillUpdateRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'number' => 'required|string|min:5|unique:waybills,number,' . $this->waybill->id,
            'company_id' => 'required|integer',
            'corporation_id' => 'required|integer',
            'address' => 'required|string',
            'status' => 'required|string',
            'content' => 'required|string',
        ];
    }

    public function attributes()
    {
        return [
            'company_id' => 'Company',
            'corporation_id' => 'Corporation',
            'address' => 'Address',
            'status' => 'Status',
            'content' => 'Content',
        ];
    }
}
