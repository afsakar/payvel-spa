<?php

namespace App\Http\Controllers\Api;

use App\Models\Bill;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Bill\BillStoreRequest;
use App\Http\Requests\Bill\BillUpdateRequest;
use App\Http\Resources\BillResource;
use App\Models\Company;
use App\Models\BillItem;
use App\Models\Waybill;
use App\Models\WaybillItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BillController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        $company = Company::withTrashed()->findOrFail($id);
        $bills = $company->bills()->with(['corporation', 'waybill', 'withholding']);

        if (request()->has('all') && request()->all == 'true') {
            $paginateList = $bills->get();
        } else {
            $paginateList = $bills->when(request()->has('search'), function ($query) {
                $query->where('number', 'like', '%' . request()->search . '%')
                    ->orWhereHas('corporation', function ($query) {
                        $query->where('name', 'like', '%' . request()->search . '%');
                    });
            })->when(request()->has('sort'), function ($query) {
                $query->orderBy(request()->order, request()->sort);
            })->fastPaginate(5);
        }
        return BillResource::collection($paginateList);
    }

    public function trash(Company $company)
    {
        $bills = $company->bills()->onlyTrashed()->with(['corporation', 'waybill', 'withholding']);

        $paginateList = $bills->when(request()->has('search'), function ($query) {
            $query->where('number', 'like', '%' . request()->search . '%');
        })->when(request()->has('sort'), function ($query) {
            $query->orderBy(request()->order, request()->sort);
        })->fastPaginate(5);

        return BillResource::collection($paginateList);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(BillStoreRequest $request)
    {
        try {
            DB::transaction(function () use ($request) {
                $bill = Bill::create($request->validated());

                $waybill = Waybill::findOrFail($request->waybill_id)->load('items');

                foreach ($waybill->items as $item) {
                    BillItem::create([
                        'bill_id' => $bill->id,
                        'material_id' => $item->material_id,
                        'quantity' => $item->quantity,
                        'price' => $item->price
                    ]);
                }

                Log::info('Bill created successfully', [
                    'bill' => $bill,
                ]);

                return response()->json([
                    'message' => 'Bill created successfully'
                ], 201);
            });
        } catch (\Throwable $th) {
            Log::error('Error creating bill', [
                'error' => $th->getMessage()
            ]);

            return response()->json([
                'message' => 'Error creating bill'
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Bill  $bill
     * @return \Illuminate\Http\Response
     */
    public function show($bill)
    {
        $bill = Bill::with(['company', 'corporation', 'waybill', 'withholding'])->where('id', $bill)->first();
        return new BillResource($bill);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  BillUpdateRequest  $request
     * @param  \App\Models\Bill  $bill
     * @return \Illuminate\Http\Response
     */
    public function update(BillUpdateRequest $request, Bill $bill)
    {
        try {
            DB::transaction(function () use ($request, $bill) {
                if ($bill->waybill_id != $request->waybill_id) {
                    $bill->items()->delete();

                    $waybill = Waybill::findOrFail($request->waybill_id)->load('items');

                    foreach ($waybill->items as $item) {
                        BillItem::create([
                            'bill_id' => $bill->id,
                            'material_id' => $item->material_id,
                            'quantity' => $item->quantity,
                            'price' => $item->price
                        ]);
                    }
                }

                $bill->update($request->validated());

                Log::info('Bill updated successfully', [
                    'bill' => $bill,
                ]);

                return response()->json([
                    'message' => 'Bill updated successfully'
                ], 201);
            });
        } catch (\Throwable $th) {
            Log::error('Error updating bill', [
                'error' => $th->getMessage()
            ]);

            return response()->json([
                'message' => 'Error updating bill'
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Bill  $bill
     * @return \Illuminate\Http\Response
     */
    public function destroy(Bill $bill)
    {
        try {
            $bill->delete();

            Log::info('Bill deleted successfully', [
                'waybill' => $bill,
            ]);

            return response()->json([
                'message' => 'Bill deleted successfully'
            ], 201);
        } catch (\Throwable $th) {
            Log::error('Error deleting bill', [
                'error' => $th->getMessage()
            ]);

            return response()->json([
                'message' => 'Error deleting bill'
            ], 500);
        }
    }

    public function restore($id)
    {
        try {
            $bill = Bill::onlyTrashed()->findOrFail($id);
            $bill->restore();

            Log::info('Bill restored successfully', [
                'bill' => $bill,
            ]);

            return response()->json([
                'message' => 'Bill restored successfully'
            ], 201);
        } catch (\Throwable $th) {
            Log::error('Error restoring bill', [
                'error' => $th->getMessage()
            ]);

            return response()->json([
                'message' => 'Error restoring bill'
            ], 500);
        }
    }

    public function forceDelete($id)
    {
        try {
            $bill = Bill::onlyTrashed()->findOrFail($id);
            $bill->items()->delete();
            $bill->forceDelete();

            Log::info('Bill force deleted successfully', [
                'bill' => $bill,
            ]);

            return response()->json([
                'message' => 'Bill force deleted successfully'
            ], 201);
        } catch (\Throwable $th) {
            Log::error('Error force deleting bill', [
                'error' => $th->getMessage()
            ]);

            return response()->json([
                'message' => 'Error force deleting bill'
            ], 500);
        }
    }

    public function saveBillItems(Request $request, $id)
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
            'items.*.bill_id' => 'required|integer',
            'waybill_id' => 'required|integer',
        ]);


        try {
            DB::transaction(function () use ($request, $id) {
                $bill = Bill::findOrFail($id);
                $bill->items()->delete();

                $waybill = Waybill::findOrFail($request->waybill_id);
                $waybill->items()->delete();

                $bill->update([
                    'discount' => $request->discount ?? $bill->discount,
                ]);

                foreach ($request->items as $item) {
                    BillItem::create([
                        'bill_id' => $bill->id,
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

                Log::info('Bill items saved successfully', [
                    'bill' => $bill,
                ]);

                return response()->json([
                    'message' => 'Bill items saved successfully'
                ], 201);
            });
        } catch (\Throwable $th) {
            Log::error('Error saving bill items', [
                'error' => $th->getMessage()
            ]);

            return response()->json([
                'message' => 'Error saving bill items'
            ], 500);
        }
    }
}
