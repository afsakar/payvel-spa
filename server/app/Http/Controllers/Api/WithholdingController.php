<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\WithholdingResource;
use App\Models\Withholding;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WithholdingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        $withHoldings = Withholding::all();
        $deletedWithHoldings = Withholding::onlyTrashed()->get();

        return WithholdingResource::collection($withHoldings)->additional([
            'deletedWithholdings' => $deletedWithHoldings
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            Withholding::create([
                'name' => $request->name,
                'rate' => $request->rate
            ]);

            Log::info('Withholding Tax created!');

            return response()->json(['message' => 'Withholding has been created successfully!']);
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
     * @return WithholdingResource
     */
    public function show(Withholding $withholding)
    {
        return new WithholdingResource($withholding);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Withholding $withholding)
    {
        try {
            $withholding->name = $request->name;
            $withholding->rate = $request->rate;
            $withholding->save();

            Log::info('Withholding ID no: '. $withholding->id . ', has updated!');

            return response()->json(['message' => 'Withholding has been updated successfully!']);
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
    public function destroy(Withholding $withholding)
    {
        try {
            $withholding->delete();

            Log::info('Tax ID no: '. $withholding->id . ', has deleted!');

            return response()->json(['message' => 'Withholding has been deleted successfully!']);
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
        $withholding = Withholding::onlyTrashed()->findOrFail($id);
        $withholding->restore();
        Log::info('Withholding ID no: '. $withholding->id . ', has restored!');
        return response()->json(['message' => 'Withholding has been restored successfully!']);
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
            $withholding = Withholding::onlyTrashed()->where('id', $id)->first();
            $withholding->forceDelete();

            Log::info('Withholding ID no: '. $withholding->id . ', has force deleted!');

            return response()->json(['message' => 'Withholding has been force deleted successfully!']);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 422);
        }
    }
}
