<?php

namespace App\Http\Requests\Revenue;

use Illuminate\Foundation\Http\FormRequest;

class RevenueStoreRequest extends FormRequest
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
            'account_id' => 'required|integer|exists:accounts,id',
            'company_id' => 'required|integer|exists:companies,id',
            'category_id' => 'required|integer|exists:categories,id',
            'corporation_id' => 'required|integer|exists:corporations,id',
            'description' => 'required|string',
            'amount' => 'required|numeric',
            'type' => 'required|string',
            'due_at' => 'required|date'
        ];
    }

    public function attributes()
    {
        return [
            'account_id' => 'Account',
            'company_id' => 'Company',
            'category_id' => 'Category',
            'corporation_id' => 'Corporation',
            'description' => 'Description',
            'amount' => 'Amount',
            'type' => 'Type',
            'due_at' => 'Due At'
        ];
    }
}
