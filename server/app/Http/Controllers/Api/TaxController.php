<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tax\TaxStoreRequest;
use App\Http\Requests\Tax\TaxUpdateRequest;
use App\Http\Resources\TaxResource;
use App\Models\Tax;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TaxController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        $taxes = Tax::all();
        $deletedTaxes = Tax::onlyTrashed()->get();

        return TaxResource::collection($taxes)->additional([
            'deletedTaxes' => $deletedTaxes
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(TaxStoreRequest $request)
    {
        try {
            Tax::create([
                'name' => $request->name,
                'rate' => $request->rate
            ]);

            Log::info('New Tax created!');

            return response()->json(['message' => 'Tax has been created successfully!']);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 422);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Tax  $tax
     * @return TaxResource
     */
    public function show(Tax $tax)
    {
        return new TaxResource($tax);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Tax  $tax
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(TaxUpdateRequest $request, Tax $tax)
    {
        try {
            $tax->name = $request->name;
            $tax->rate = $request->rate;
            $tax->save();

            Log::info('Tax ID no: '. $tax->id . ', has updated!');

            return response()->json(['message' => 'Tax has been updated successfully!']);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Tax  $tax
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Tax $tax)
    {
        try {
            $tax->delete();

            Log::info('Tax ID no: '. $tax->id . ', has deleted!');

            return response()->json(['message' => 'Tax has been deleted successfully!']);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 422);
        }
    }

    /**
     * Restore the specified resource from storage.
     *
     * @param  \App\Models\Tax  $tax
     * @return \Illuminate\Http\JsonResponse
     */
    public function restore($id)
    {
        $tax = Tax::onlyTrashed()->where('id', $id)->first();
        $tax->restore();
        Log::info('Tax ID no: '. $tax->id . ', has restored!');
        return response()->json(['message' => 'Tax has been restored successfully!']);
    }

    /**
     * Force Delete the specified resource from storage.
     *
     * @param  \App\Models\Tax  $tax
     * @return \Illuminate\Http\JsonResponse
     */
    public function forceDelete(Tax $tax)
    {
        try {
            $tax->forceDelete();

            Log::info('Tax ID no: '. $tax->id . ', has force deleted!');

            return response()->json(['message' => 'Tax has been force deleted successfully!']);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 422);
        }
    }
}
