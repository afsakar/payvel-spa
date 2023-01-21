<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Corporation\CorporationStoreRequest;
use App\Http\Requests\Corporation\CorporationUpdateRequest;
use App\Http\Resources\CorporationResource;
use App\Models\Corporation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CorporationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $corporations = Corporation::all();
        $deletedCorporations = Corporation::onlyTrashed()->get();

        return CorporationResource::collection($corporations)->additional([
            'deleted_corporations' => $deletedCorporations,
        ]);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CorporationStoreRequest $request)
    {
        try {
            DB::transaction(function () use ($request) {
                Corporation::create([
                    'name' => $request->name,
                    'owner' => $request->owner,
                    'tel_number' => $request->tel_number,
                    'gsm_number' => $request->gsm_number,
                    'fax_number' => $request->fax_number,
                    'email' => $request->email,
                    'address' => $request->address,
                    'tax_office' => $request->tax_office,
                    'tax_number' => $request->tax_number,
                    'type' => $request->type,
                    'currency_id' => $request->currency_id,
                ]);

                Log::info('New Corporation created!');

                return response()->json(['message' => 'Corporation has been created successfully!']);
            });
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
    public function show(Corporation $corporation)
    {
        return new CorporationResource($corporation);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CorporationUpdateRequest $request, Corporation $corporation)
    {
        try {
            DB::transaction(function () use ($request, $corporation) {
                $corporation->name = $request->name;
                $corporation->owner = $request->owner;
                $corporation->tel_number = $request->tel_number;
                $corporation->gsm_number = $request->gsm_number;
                $corporation->fax_number = $request->fax_number;
                $corporation->email = $request->email;
                $corporation->address = $request->address;
                $corporation->tax_office = $request->tax_office;
                $corporation->tax_number = $request->tax_number;
                $corporation->type = $request->type;
                $corporation->currency_id = $request->currency_id;
                $corporation->save();

                Log::info('Corporation ID no: ' . $corporation->id . ', has updated!');

                return response()->json(['message' => 'Corporation has been updated successfully!']);
            });
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
    public function destroy(Corporation $corporation)
    {
        try {
            $corporation->delete();

            Log::info('Corporation ID no: ' . $corporation->id . ', has deleted!');

            return response()->json([
                'message' => 'Corporation has been deleted successfully!',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),
            ], 422);
        }
    }

    public function restore($id)
    {
        try {
            $corporation = Corporation::onlyTrashed()->where('id', $id)->first();
            $corporation->restore();

            Log::info('Corporation ID no: ' . $corporation->id . ', has restored!');

            return response()->json([
                'message' => 'Corporation has been restored successfully!',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),
            ], 422);
        }
    }

    public function forceDelete($id)
    {
        try {
            $corporation = Corporation::onlyTrashed()->where('id', $id)->first();
            $corporation->forceDelete();

            Log::info('Corporation ID no: ' . $corporation->id . ', has force deleted!');

            return response()->json([
                'message' => 'Corporation has been force deleted successfully!',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),
            ], 422);
        }
    }
}
