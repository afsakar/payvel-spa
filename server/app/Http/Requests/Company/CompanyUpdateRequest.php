<?php

namespace App\Http\Requests\Company;

use Illuminate\Foundation\Http\FormRequest;

class CompanyUpdateRequest extends FormRequest
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
            'name' => 'required|min:3|unique:companies,name,' . $this->company->id,
            'owner' => 'nullable',
            'tel_number' => 'required',
            'gsm_number' => 'nullable',
            'fax_number' => 'nullable',
            'email' => 'nullable',
            'address' => 'nullable',
            'tax_office' => 'nullable',
            'tax_number' => 'nullable|max:11|unique:companies,tax_number,' . $this->company->id,
            'logo' => $this->company->logo != null ? 'nullable|image|max:2048' : 'nullable'
        ];
    }

    public function attributes()
    {
        return [
            'name' => 'Name',
            'owner' => 'Owner name',
            'tel_number' => 'Tel number',
            'gsm_number' => 'GSM number',
            'fax_number' => 'Fax number',
            'email' => 'Email address',
            'address' => 'Address',
            'tax_office' => 'Tax office',
            'tax_number' => 'ID number/Tax number',
            'logo' => 'Logo',
        ];
    }
}
