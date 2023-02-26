<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tax\TaxStoreRequest;
use App\Http\Requests\Tax\TaxUpdateRequest;
use App\Http\Resources\TaxResource;
use App\Models\Tax;
use Illuminate\Http\JsonResponse;
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
        if (request()->has('all') && request()->all == 'true') {
            $taxes = Tax::when(request()->has('search'), function ($query) {
                $query->where('name', 'like', '%' . request()->search . '%');
            })->when(request()->has('sort'), function ($query) {
                $query->orderBy(request()->order, request()->sort);
            })->get();

            return TaxResource::collection($taxes);
        } else {
            $taxes = Tax::when(request()->has('search'), function ($query) {
                $query->where('name', 'like', '%' . request()->search . '%');
            })->when(request()->has('sort'), function ($query) {
                $query->orderBy(request()->order, request()->sort);
            })->paginate(5);

            return TaxResource::collection($taxes);
        }
    }

    public function trash()
    {
        $taxes = Tax::onlyTrashed()->when(request()->has('search'), function ($query) {
            $query->where('name', 'like', '%' . request()->search . '%');
        })->when(request()->has('sort'), function ($query) {
            $query->orderBy(request()->order, request()->sort);
        })->paginate(5);

        return TaxResource::collection($taxes);
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
     * @param Tax $tax
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
     * @param Tax $tax
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(TaxUpdateRequest $request, Tax $tax)
    {
        try {
            $tax->name = $request->name;
            $tax->rate = $request->rate;
            $tax->save();

            Log::info('Tax ID no: ' . $tax->id . ', has updated!');

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
     * @param Tax $tax
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Tax $tax)
    {
        try {
            $tax->delete();

            Log::info('Tax ID no: ' . $tax->id . ', has deleted!');

            return response()->json(['message' => 'Tax has been deleted successfully!']);
        } catch (\Throwable $th) {
            Log::error('Error deleting tax', [
                'error' => $th->getMessage()
            ]);

            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Restore the specified resource from storage.
     *
     * @param Tax $tax
     * @return \Illuminate\Http\JsonResponse
     */
    public function restore($id)
    {
        $tax = Tax::onlyTrashed()->where('id', $id)->first();
        $tax->restore();
        Log::info('Tax ID no: ' . $tax->id . ', has restored!');
        return response()->json(['message' => 'Tax has been restored successfully!']);
    }

    /**
     * Force Delete the specified resource from storage.
     *
     * @param $id
     * @return JsonResponse
     */
    public function forceDelete($id)
    {
        try {
            $tax = Tax::onlyTrashed()->where('id', $id)->first();
            $tax->forceDelete();

            Log::info('Tax ID no: ' . $tax->id . ', has force deleted!');

            return response()->json(['message' => 'Tax has been force deleted successfully!']);
        } catch (\Throwable $th) {
            Log::error('Error force deleting tax', [
                'error' => $th->getMessage()
            ]);
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }
}
