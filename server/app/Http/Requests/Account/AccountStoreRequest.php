<?php

namespace App\Http\Requests\Account;

use Illuminate\Foundation\Http\FormRequest;

class AccountStoreRequest extends FormRequest
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
            'name' => 'required|min:3|unique:accounts,name',
            'description' => 'nullable',
            'balance' => 'required|numeric',
            'account_type_id' => 'required|exists:account_types,id',
            'currency_id' => 'required|exists:currencies,id',
        ];
    }

    public function attributes()
    {
        return [
            'name' => 'Name',
            'description' => 'Description',
            'balance' => 'Balance',
            'account_type_id' => 'Account type',
            'currency_id' => 'Currency',
        ];
    }
}
