<?php

namespace App\Http\Requests\Agreement;

use Illuminate\Foundation\Http\FormRequest;

class AgreementStoreRequest extends FormRequest
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
            'name' => 'required|string|unique:agreements,name',
            'company_id' => 'required|integer|exists:companies,id',
            'corporation_id' => 'required|integer|exists:corporations,id',
            'content' => 'nullable|string',
        ];
    }

    public function attributes()
    {
        return [
            'name' => 'Agreement Name',
            'company_id' => 'Company',
            'corporation_id' => 'Corporation',
            'content' => 'Agreement Content'
        ];
    }
}
