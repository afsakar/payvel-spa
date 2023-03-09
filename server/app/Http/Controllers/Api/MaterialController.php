<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Material\MaterialStoreRequest;
use App\Http\Requests\Material\MaterialUpdateRequest;
use App\Http\Resources\MaterialResource;
use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MaterialController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (request()->has('all') && request()->all == 'true') {
            $materials = Material::all();

            return MaterialResource::collection($materials);
        } else {
            $materials = Material::when(request()->has('search'), function ($query) {
                $query->where('name', 'like', '%' . request()->search . '%');
            })->when(request()->has('sort'), function ($query) {
                $query->orderBy(request()->order, request()->sort);
            })->fastPaginate(5);

            return MaterialResource::collection($materials);
        }
    }

    public function trash()
    {
        $currencies = Material::onlyTrashed()->when(request()->has('search'), function ($query) {
            $query->where('name', 'like', '%' . request()->search . '%');
        })->when(request()->has('sort'), function ($query) {
            $query->orderBy(request()->order, request()->sort);
        })->fastPaginate(5);

        return MaterialResource::collection($currencies);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(MaterialStoreRequest $request)
    {
        try {
            $material = Material::create([
                'unit_id' => $request->unit_id,
                'tax_id' => $request->tax_id,
                'currency_id' => $request->currency_id,
                'name' => $request->name,
                'description' => $request->description,
                'code' => $request->code,
                'price' => $request->price,
                'category' => $request->category,
                'type' => $request->type,
            ]);

            Log::info('Material created successfully', [
                'material' => $material
            ]);

            return response()->json([
                'message' => 'Material created successfully'
            ], 201);
        } catch (\Throwable $th) {
            Log::error('Error creating material', [
                'error' => $th->getMessage()
            ]);

            return response()->json([
                'message' => 'Error creating material'
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Material $material)
    {
        return new MaterialResource($material);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(MaterialUpdateRequest $request, Material $material)
    {
        try {
            $material->unit_id = $request->unit_id;
            $material->tax_id = $request->tax_id;
            $material->currency_id = $request->currency_id;
            $material->name = $request->name;
            $material->description = $request->description;
            $material->code = $request->code;
            $material->price = $request->price;
            $material->category = $request->category;
            $material->type = $request->type;
            $material->save();

            Log::info('Material updated successfully', [
                'material' => $material
            ]);

            return response()->json([
                'message' => 'Material updated successfully'
            ], 200);
        } catch (\Throwable $th) {
            Log::error('Error updating material', [
                'error' => $th->getMessage()
            ]);

            return response()->json([
                'message' => 'Error updating material'
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Material $material)
    {
        try {
            $material->delete();

            Log::info('Material deleted successfully', [
                'material' => $material
            ]);

            return response()->json([
                'message' => 'Material deleted successfully'
            ], 200);
        } catch (\Throwable $th) {
            Log::error('Error deleting material', [
                'error' => $th->getMessage()
            ]);

            return response()->json([
                'message' => 'Error deleting material'
            ], 500);
        }
    }

    public function restore($id)
    {
        try {
            $material = Material::onlyTrashed()->where('id', $id)->first();
            $material->restore();

            Log::info('Material ID no: ' . $material->id . ', has restored!');

            return response()->json(['message' => 'Material has been restored successfully!']);
        } catch (\Throwable $th) {
            Log::error('Error restoring material', [
                'error' => $th->getMessage()
            ]);

            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function forceDelete($id)
    {
        try {
            $material = Material::onlyTrashed()->where('id', $id)->first();
            $material->forceDelete();

            Log::info('Material ID no: ' . $material->id . ', has force deleted!');

            return response()->json(['message' => 'Material has been deleted successfully!']);
        } catch (\Throwable $th) {
            Log::error('Error force deleting material', [
                'error' => $th->getMessage()
            ]);

            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }
}
