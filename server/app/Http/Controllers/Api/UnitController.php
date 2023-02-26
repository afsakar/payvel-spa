<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Unit\UnitStoreRequest;
use App\Http\Requests\Unit\UnitUpdateRequest;
use App\Http\Resources\UnitResource;
use App\Models\Unit;
use Illuminate\Support\Facades\Log;

class UnitController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (request()->has('all') && request()->all == 'true') {
            $units = Unit::when(request()->has('search'), function ($query) {
                $query->where('name', 'like', '%' . request()->search . '%');
            })->when(request()->has('sort'), function ($query) {
                $query->orderBy(request()->order, request()->sort);
            })->get();

            return UnitResource::collection($units);
        } else {
            $units = Unit::when(request()->has('search'), function ($query) {
                $query->where('name', 'like', '%' . request()->search . '%');
            })->when(request()->has('sort'), function ($query) {
                $query->orderBy(request()->order, request()->sort);
            })->paginate(5);

            return UnitResource::collection($units);
        }
    }

    public function trash()
    {
        $units = Unit::onlyTrashed()->when(request()->has('search'), function ($query) {
            $query->where('name', 'like', '%' . request()->search . '%');
        })->when(request()->has('sort'), function ($query) {
            $query->orderBy(request()->order, request()->sort);
        })->paginate(5);

        return UnitResource::collection($units);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Unit\UnitStoreRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UnitStoreRequest $request)
    {
        try {
            Unit::create($request->validated());

            Log::info('New Unit created!');

            return response()->json(['message' => 'Unit has been created successfully!']);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 422);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Unit $unit)
    {
        return new UnitResource($unit);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UnitUpdateRequest $request, Unit $unit)
    {
        try {
            $unit->name = $request->name;
            $unit->save();

            Log::info('Unit ID no: ' . $unit->id . ', has updated!');

            return response()->json(['message' => 'Unit has been updated successfully!']);
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
     * @return \Illuminate\Http\Response
     */
    public function destroy(Unit $unit)
    {
        try {
            $unit->delete();

            Log::info('Unit ID no: ' . $unit->id . ', has deleted!');

            return response()->json(['message' => 'Unit has been deleted successfully!']);
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
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        try {
            $unit = Unit::onlyTrashed()->where('id', $id)->first();
            $unit->restore();

            Log::info('Unit ID no: ' . $unit->id . ', has restored!');

            return response()->json(['message' => 'Unit has been restored successfully!']);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource permanently from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function forceDelete($id)
    {
        try {
            $unit = Unit::onlyTrashed()->where('id', $id)->first();
            $unit->forceDelete();

            Log::info('Unit ID no: ' . $unit->id . ', has permanently deleted!');

            return response()->json(['message' => 'Unit has been permanently deleted successfully!']);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }
}
