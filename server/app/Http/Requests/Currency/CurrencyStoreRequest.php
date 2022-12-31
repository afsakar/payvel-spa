<?php

namespace App\Http\Requests\Currency;

use Illuminate\Foundation\Http\FormRequest;

class CurrencyStoreRequest extends FormRequest
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
            'code' => 'required|min:3|unique:currencies,code',
            'name' => 'required|min:3|unique:currencies,name',
            'symbol' => 'required|min:1|max:1',
            'position' => 'required|in:after,before'
        ];
    }

    /**
     * @return string[]
     */
    public function attributes()
    {
        return [
            'code' => 'Code',
            'name' => 'Name',
            'symbol' => 'Symbol',
            'position' => 'Position'
        ];
    }
}
