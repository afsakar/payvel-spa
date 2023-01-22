<?php

namespace App\Http\Requests\Material;

use Illuminate\Foundation\Http\FormRequest;

class MaterialStoreRequest extends FormRequest
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
            'unit_id' => 'required',
            'tax_id' => 'required',
            'currency_id' => 'required',
            'name' => 'required|unique:materials,name',
            'description' => 'nullable',
            'code' => 'required|unique:materials,code',
            'price' => 'required|numeric',
            'category' => 'required',
            'type' => 'required',
        ];
    }

    public function attributes()
    {
        return [
            'unit_id' => 'Unit',
            'tax_id' => 'Tax',
            'currency_id' => 'Currency',
            'name' => 'Name',
            'description' => 'Description',
            'code' => 'Code',
            'price' => 'Price',
            'category' => 'Category',
            'type' => 'Type',
        ];
    }
}
