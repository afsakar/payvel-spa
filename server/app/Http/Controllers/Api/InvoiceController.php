<?php

namespace App\Http\Controllers\Api;

use App\Models\Invoice;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Invoice\InvoiceStoreRequest;
use App\Http\Requests\Invoice\InvoiceUpdateRequest;
use App\Http\Resources\InvoiceResource;
use App\Models\Company;
use App\Models\InvoiceItem;
use App\Models\Waybill;
use App\Models\WaybillItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        $company = Company::withTrashed()->findOrFail($id);

        if (request()->has('all') && request()->all == 'true') {
            $invoices = $company->invoices()->with(['corporation', 'waybill', 'withholding']);

            $paginateList = $invoices->when(request()->has('search'), function ($query) {
                $query->where('number', 'like', '%' . request()->search . '%')
                    ->orWhereHas('corporation', function ($query) {
                        $query->where('name', 'like', '%' . request()->search . '%');
                    });
            })->when(request()->has('sort'), function ($query) {
                $query->orderBy(request()->order, request()->sort);
            })->get();

            return InvoiceResource::collection($paginateList);
        } else {
            $invoices = $company->invoices()->with(['corporation', 'waybill', 'withholding']);

            $paginateList = $invoices->when(request()->has('search'), function ($query) {
                $query->where('number', 'like', '%' . request()->search . '%')
                    ->orWhereHas('corporation', function ($query) {
                        $query->where('name', 'like', '%' . request()->search . '%');
                    });
            })->when(request()->has('sort'), function ($query) {
                $query->orderBy(request()->order, request()->sort);
            })->fastPaginate(5);

            return InvoiceResource::collection($paginateList);
        }
    }

    public function trash(Company $company)
    {
        $invoices = $company->invoices()->onlyTrashed()->with(['corporation', 'waybill', 'withholding']);

        $paginateList = $invoices->when(request()->has('search'), function ($query) {
            $query->where('number', 'like', '%' . request()->search . '%');
        })->when(request()->has('sort'), function ($query) {
            $query->orderBy(request()->order, request()->sort);
        })->fastPaginate(5);

        return InvoiceResource::collection($paginateList);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(InvoiceStoreRequest $request)
    {
        try {
            DB::transaction(function () use ($request) {
                $invoice = Invoice::create($request->validated());

                $waybill = Waybill::findOrFail($request->waybill_id)->load('items');

                foreach ($waybill->items as $item) {
                    InvoiceItem::create([
                        'invoice_id' => $invoice->id,
                        'material_id' => $item->material_id,
                        'quantity' => $item->quantity,
                        'price' => $item->price
                    ]);
                }

                Log::info('Invoice created successfully', [
                    'invoice' => $invoice,
                ]);

                return response()->json([
                    'message' => 'Invoice created successfully'
                ], 201);
            });
        } catch (\Throwable $th) {
            Log::error('Error creating invoice', [
                'error' => $th->getMessage()
            ]);

            return response()->json([
                'message' => 'Error creating invoice'
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function show($invoice)
    {
        $invoice = Invoice::with(['company', 'corporation', 'waybill', 'withholding'])->where('id', $invoice)->first();
        return new InvoiceResource($invoice);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function update(InvoiceUpdateRequest $request, Invoice $invoice)
    {
        try {
            DB::transaction(function () use ($request, $invoice) {
                if ($invoice->waybill_id != $request->waybill_id) {
                    $invoice->items()->delete();

                    $waybill = Waybill::findOrFail($request->waybill_id)->load('items');

                    foreach ($waybill->items as $item) {
                        InvoiceItem::create([
                            'invoice_id' => $invoice->id,
                            'material_id' => $item->material_id,
                            'quantity' => $item->quantity,
                            'price' => $item->price
                        ]);
                    }
                }

                $invoice->update($request->validated());

                Log::info('Invoice updated successfully', [
                    'invoice' => $invoice,
                ]);

                return response()->json([
                    'message' => 'Invoice updated successfully'
                ], 201);
            });
        } catch (\Throwable $th) {
            Log::error('Error updating invoice', [
                'error' => $th->getMessage()
            ]);

            return response()->json([
                'message' => 'Error updating invoice'
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function destroy(Invoice $invoice)
    {
        try {
            $invoice->delete();

            Log::info('Invoice deleted successfully', [
                'waybill' => $invoice,
            ]);

            return response()->json([
                'message' => 'Invoice deleted successfully'
            ], 201);
        } catch (\Throwable $th) {
            Log::error('Error deleting invoice', [
                'error' => $th->getMessage()
            ]);

            return response()->json([
                'message' => 'Error deleting invoice'
            ], 500);
        }
    }

    public function restore($id)
    {
        try {
            $invoice = Invoice::onlyTrashed()->findOrFail($id);
            $invoice->restore();

            Log::info('Invoice restored successfully', [
                'invoice' => $invoice,
            ]);

            return response()->json([
                'message' => 'Invoice restored successfully'
            ], 201);
        } catch (\Throwable $th) {
            Log::error('Error restoring invoice', [
                'error' => $th->getMessage()
            ]);

            return response()->json([
                'message' => 'Error restoring invoice'
            ], 500);
        }
    }

    public function forceDelete($id)
    {
        try {
            $invoice = Invoice::onlyTrashed()->findOrFail($id);
            $invoice->items()->delete();
            $invoice->forceDelete();

            Log::info('Invoice force deleted successfully', [
                'invoice' => $invoice,
            ]);

            return response()->json([
                'message' => 'Invoice force deleted successfully'
            ], 201);
        } catch (\Throwable $th) {
            Log::error('Error force deleting invoice', [
                'error' => $th->getMessage()
            ]);

            return response()->json([
                'message' => 'Error force deleting invoice'
            ], 500);
        }
    }

    public function saveInvoiceItems(Request $request, $id)
    {
        $request->merge([
            'items' => json_decode($request->items, true),
            'waybill_id' => $request->waybill_id ?? '',
            'discount' => $request->discount ?? ''
        ]);

        $request->validate([
            'items' => 'required|array',
            'items.*' => 'required|array',
            'items.*.material_id' => 'required|integer',
            'items.*.quantity' => 'required|integer',
            'items.*.price' => 'required|numeric',
            'items.*.invoice_id' => 'required|integer',
            'waybill_id' => 'required|integer',
        ]);


        try {
            DB::transaction(function () use ($request, $id) {
                $invoice = Invoice::findOrFail($id);
                $invoice->items()->delete();

                $waybill = Waybill::findOrFail($request->waybill_id);
                $waybill->items()->delete();

                $invoice->update([
                    'discount' => $request->discount ?? $invoice->discount,
                ]);

                foreach ($request->items as $item) {
                    InvoiceItem::create([
                        'invoice_id' => $invoice->id,
                        'material_id' => $item['material_id'],
                        'quantity' => $item['quantity'],
                        'price' => $item['price'],
                    ]);

                    WaybillItem::create([
                        'waybill_id' => $waybill->id,
                        'material_id' => $item['material_id'],
                        'quantity' => $item['quantity'],
                        'price' => $item['price'],
                    ]);
                }

                Log::info('Invoice items saved successfully', [
                    'invoice' => $invoice,
                ]);

                return response()->json([
                    'message' => 'Invoice items saved successfully'
                ], 201);
            });
        } catch (\Throwable $th) {
            Log::error('Error saving invoice items', [
                'error' => $th->getMessage()
            ]);

            return response()->json([
                'message' => 'Error saving invoice items'
            ], 500);
        }
    }
}
