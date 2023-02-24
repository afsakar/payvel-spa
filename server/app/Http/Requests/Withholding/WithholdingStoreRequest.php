<?php

namespace App\Http\Requests\Withholding;

use Illuminate\Foundation\Http\FormRequest;

class WithholdingStoreRequest extends FormRequest
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
            'name' => 'string|required|unique:withholdings,name',
            'rate' => 'numeric|required',
        ];
    }

    public function attributes()
    {
        return [
            'name' => 'Name',
            'rate' => 'Rate',
        ];
    }
}
