<?php

namespace App\Http\Requests\Corporation;

use Illuminate\Foundation\Http\FormRequest;

class CorporationUpdateRequest extends FormRequest
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
            'name' => 'required|string|unique:corporations,name,' . $this->corporation->id,
            'owner' => 'nullable|string',
            'tel_number' => 'nullable|string',
            'gsm_number' => 'nullable|string',
            'fax_number' => 'nullable|string',
            'email' => 'nullable|string',
            'address' => 'nullable|string',
            'tax_office' => 'nullable|string',
            'tax_number' => 'nullable|string|max:11|unique:corporations,tax_number,' . $this->corporation->id,
            'type' => 'required|string',
            'currency_id' => 'required',
        ];
    }

    public function attributes()
    {
        return [
            'name' => 'Corporation Name',
            'owner' => 'Owner Name',
            'tel_number' => 'Telephone Number',
            'gsm_number' => 'GSM Number',
            'fax_number' => 'Fax Number',
            'email' => 'E-Mail',
            'address' => 'Address',
            'tax_office' => 'Tax Office',
            'tax_number' => 'Tax Number',
            'type' => 'Corporation Type',
            'currency_id' => 'Currency',
        ];
    }
}
