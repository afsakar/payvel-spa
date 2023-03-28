<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Waybill\WaybillStoreRequest;
use App\Http\Requests\Waybill\WaybillUpdateRequest;
use App\Http\Resources\WaybillResource;
use App\Models\Company;
use App\Models\Waybill;
use App\Models\WaybillItem;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WaybillController extends Controller
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
            if (request()->has('corporation_id') && request()->has('waybill_id')) {
                $waybills = $company->waybills()->with('corporation')->where('id', request()->waybill_id);
                $otherWaybills = $company->waybills()->with('corporation')->where('corporation_id', request()->corporation_id)->whereDoesntHave('bills')->whereDoesntHave('invoices')->where('id', '!=', request()->waybill_id);

                $waybills = $waybills->union($otherWaybills);
            } else {
                $waybills = $company->waybills()->with('corporation')->whereDoesntHave('bills')->whereDoesntHave('invoices');
            }
            $paginateList = $waybills->get();

            return WaybillResource::collection($paginateList);
        } else {
            $waybills = $company->waybills()->with('corporation');

            $paginateList = $waybills->when(request()->has('search'), function ($query) {
                $query->where('number', 'like', '%' . request()->search . '%')
                    ->orWhereHas('corporation', function ($query) {
                        $query->where('name', 'like', '%' . request()->search . '%');
                    });
            })->when(request()->has('sort'), function ($query) {
                $query->orderBy(request()->order, request()->sort);
            })->fastPaginate(5);

            return WaybillResource::collection($paginateList);
        }
    }

    public function trash(Company $company)
    {
        $waybills = $company->waybills()->onlyTrashed()->with('corporation');

        $paginateList = $waybills->when(request()->has('search'), function ($query) {
            $query->where('number', 'like', '%' . request()->search . '%');
        })->when(request()->has('sort'), function ($query) {
            $query->orderBy(request()->order, request()->sort);
        })->fastPaginate(5);

        return WaybillResource::collection($paginateList);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(WaybillStoreRequest $request)
    {
        try {
            DB::transaction(function () use ($request) {
                $waybill = Waybill::create($request->validated());

                Log::info('Waybill created successfully', [
                    'waybill' => $waybill,
                ]);

                return response()->json([
                    'message' => 'Waybill created successfully'
                ], 201);
            });
        } catch (\Throwable $th) {
            Log::error('Error creating waybill', [
                'error' => $th->getMessage()
            ]);

            return response()->json([
                'message' => 'Error creating waybill'
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($waybill)
    {
        $waybill = Waybill::with('company', 'corporation', 'items.material.unit')->where('id', $waybill)->first();
        return new WaybillResource($waybill);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(WaybillUpdateRequest $request, Waybill $waybill)
    {
        try {
            DB::transaction(function () use ($request, $waybill) {
                $waybill->update($request->validated());

                Log::info('Waybill updated successfully', [
                    'waybill' => $waybill,
                ]);

                return response()->json([
                    'message' => 'Waybill updated successfully'
                ], 201);
            });
        } catch (\Throwable $th) {
            Log::error('Error updating waybill', [
                'error' => $th->getMessage()
            ]);

            return response()->json([
                'message' => 'Error updating waybill'
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Waybill $waybill)
    {
        try {
            $waybill->delete();

            Log::info('Waybill deleted successfully', [
                'waybill' => $waybill,
            ]);

            return response()->json([
                'message' => 'Waybill deleted successfully'
            ], 201);
        } catch (\Throwable $th) {
            Log::error('Error deleting waybill', [
                'error' => $th->getMessage()
            ]);

            return response()->json([
                'message' => 'Error deleting waybill'
            ], 500);
        }
    }

    public function restore($id)
    {
        try {
            $waybill = Waybill::onlyTrashed()->findOrFail($id);
            $waybill->restore();

            Log::info('Waybill restored successfully', [
                'waybill' => $waybill,
            ]);

            return response()->json([
                'message' => 'Waybill restored successfully'
            ], 201);
        } catch (\Throwable $th) {
            Log::error('Error restoring waybill', [
                'error' => $th->getMessage()
            ]);

            return response()->json([
                'message' => 'Error restoring waybill'
            ], 500);
        }
    }

    public function forceDelete($id)
    {
        try {
            $waybill = Waybill::onlyTrashed()->findOrFail($id);
            $waybill->items()->delete();
            $waybill->forceDelete();

            Log::info('Waybill force deleted successfully', [
                'waybill' => $waybill,
            ]);

            return response()->json([
                'message' => 'Waybill force deleted successfully'
            ], 201);
        } catch (\Throwable $th) {
            Log::error('Error force deleting waybill', [
                'error' => $th->getMessage()
            ]);

            return response()->json([
                'message' => 'Error force deleting waybill'
            ], 500);
        }
    }

    public function saveWaybillItems(Request $request, $id)
    {

        $request->merge([
            'items' => json_decode($request->items, true)
        ]);

        $request->validate([
            'items' => 'required|array',
            'items.*' => 'required|array',
            'items.*.material_id' => 'required|integer',
            'items.*.quantity' => 'required|integer',
            'items.*.price' => 'required|numeric',
            'items.*.waybill_id' => 'required|integer',
        ]);


        try {
            DB::transaction(function () use ($request, $id) {
                $waybill = Waybill::findOrFail($id);
                $waybill->items()->delete();

                foreach ($request->items as $item) {
                    WaybillItem::create([
                        'waybill_id' => $waybill->id,
                        'material_id' => $item['material_id'],
                        'quantity' => $item['quantity'],
                        'price' => $item['price'],
                    ]);
                }

                Log::info('Waybill items saved successfully', [
                    'waybill' => $waybill,
                ]);

                return response()->json([
                    'message' => 'Waybill items saved successfully'
                ], 201);
            });
        } catch (\Throwable $th) {
            Log::error('Error saving waybill items', [
                'error' => $th->getMessage()
            ]);

            return response()->json([
                'message' => 'Error saving waybill items'
            ], 500);
        }
    }
}
