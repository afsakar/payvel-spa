<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Currency\CurrencyStoreRequest;
use App\Http\Requests\Currency\CurrencyUpdateRequest;
use App\Http\Resources\CurrencyResource;
use App\Models\Currency;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CurrencyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return AnonymousResourceCollection
     */
    public function index()
    {
        if (request()->has('all') && request()->all == 'true') {
            $currencies = Currency::all();
        } else {
            $currencies = Currency::when(request()->has('search'), function ($query) {
                $query->where('name', 'like', '%' . request()->search . '%');
            })->when(request()->has('sort'), function ($query) {
                $query->orderBy(request()->order, request()->sort);
            })->fastPaginate(5);
        }
        return CurrencyResource::collection($currencies);
    }

    public function trash()
    {
        $deletedItems = Currency::onlyTrashed()->when(request()->has('search'), function ($query) {
            $query->where('name', 'like', '%' . request()->search . '%');
        })->when(request()->has('sort'), function ($query) {
            $query->orderBy(request()->order, request()->sort);
        })->fastPaginate(5);

        return CurrencyResource::collection($deletedItems);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CurrencyStoreRequest $request)
    {
        try {
            Currency::create([
                'name' => $request->name,
                'code' => $request->code,
                'position' => $request->position,
                'symbol' => $request->symbol,
            ]);

            Log::info('New Currency created!');

            return response()->json(['message' => 'Currency has been created successfully!']);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 422);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param Currency $currency
     * @return CurrencyResource
     */
    public function show(Currency $currency)
    {
        return new CurrencyResource($currency);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(CurrencyUpdateRequest $request, Currency $currency)
    {
        try {
            $currency->name = $request->name;
            $currency->code = $request->code;
            $currency->position = $request->position;
            $currency->symbol = $request->symbol;
            $currency->save();

            Log::info('Currency ID no: ' . $currency->id . ', has updated!');

            return response()->json(['message' => 'Currency has been updated successfully!']);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Currency $currency)
    {
        try {
            $currency->delete();

            Log::info('Currency ID no: ' . $currency->id . ', has deleted!');

            return response()->json(['message' => 'Currency has been deleted successfully!']);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 422);
        }
    }

    /**
     * Restore the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function restore($id)
    {
        try {
            $currency = Currency::onlyTrashed()->where('id', $id)->first();
            $currency->restore();

            Log::info('Currency ID no: ' . $currency->id . ', has restored!');

            return response()->json(['message' => 'Currency has been restored successfully!']);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 422);
        }
    }

    /**
     * Force delete the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function forceDelete($id)
    {
        try {
            $currency = Currency::onlyTrashed()->where('id', $id)->first();
            $currency->forceDelete();

            Log::info('Currency ID no: ' . $currency->id . ', has force deleted!');

            return response()->json(['message' => 'Currency has been deleted successfully!']);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 422);
        }
    }
}
