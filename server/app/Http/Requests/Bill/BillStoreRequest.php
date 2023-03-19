<?php

namespace App\Http\Requests\Bill;

use Illuminate\Foundation\Http\FormRequest;

class BillStoreRequest extends FormRequest
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
            'number' => 'required|string|min:5|unique:bills,number',
            'company_id' => 'required|integer',
            'corporation_id' => 'required|integer',
            'withholding_id' => 'required|integer',
            'waybill_id' => 'required|integer',
            'notes' => 'nullable|string',
            'status' => 'required|string',
            'issue_date' => 'nullable|date',
            'discount' => 'nullable|numeric',
        ];
    }

    public function attributes()
    {
        return [
            'number' => 'Number',
            'company_id' => 'Company',
            'corporation_id' => 'Corporation',
            'withholding_id' => 'Withholding',
            'waybill_id' => 'Waybill',
            'notes' => 'Notes',
            'status' => 'Status',
            'issue_date' => 'Issue Date',
            'discount' => 'Discount',
        ];
    }
}
