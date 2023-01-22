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
        $materials = Material::all();
        $deletedMaterials = Material::onlyTrashed()->get();

        return MaterialResource::collection($materials)->additional([
            'deletedMaterials' => $deletedMaterials
        ]);

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

    public function restore(Material $material)
    {
        try {
            $material->restore();

            Log::info('Material restored successfully', [
                'material' => $material
            ]);

            return response()->json([
                'message' => 'Material restored successfully'
            ], 200);
        } catch (\Throwable $th) {
            Log::error('Error restoring material', [
                'error' => $th->getMessage()
            ]);

            return response()->json([
                'message' => 'Error restoring material'
            ], 500);
        }
    }

    public function forceDelete(Material $material)
    {
        try {
            $material->forceDelete();

            Log::info('Material force deleted successfully', [
                'material' => $material
            ]);

            return response()->json([
                'message' => 'Material force deleted successfully'
            ], 200);
        } catch (\Throwable $th) {
            Log::error('Error force deleting material', [
                'error' => $th->getMessage()
            ]);

            return response()->json([
                'message' => 'Error force deleting material'
            ], 500);
        }
    }
}
