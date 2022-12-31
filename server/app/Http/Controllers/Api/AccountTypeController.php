<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AccountType\AccountTypeStoreRequest;
use App\Http\Requests\AccountType\AccountTypeUpdateRequest;
use App\Http\Resources\AccountTypeResource;
use App\Models\AccountType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AccountTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        $accountTypes = AccountType::all();
        $deletedAccountTypes = AccountType::onlyTrashed()->get();

        return AccountTypeResource::collection($accountTypes)->additional([
            'deletedAccountTypes' => $deletedAccountTypes
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(AccountTypeStoreRequest $request)
    {
        try {
            AccountType::create([
                'name' => $request->name,
                'status' => $request->status,
            ]);

            Log::info('New Account Type created!');

            return response()->json(['message' => 'Account Type has been created successfully!']);
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
     * @return AccountTypeResource
     */
    public function show(AccountType $accountType)
    {
        return new AccountTypeResource($accountType);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(AccountTypeUpdateRequest $request, AccountType $accountType)
    {
        try {
            $accountType->name = $request->name;
            $accountType->status = $request->status;
            $accountType->save();

            Log::info('Account Type ID no: '. $accountType->id . ', has updated!');

            return response()->json(['message' => 'Account Type has been updated successfully!']);
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
    public function destroy(AccountType $accountType)
    {
        try {
            $accountType->delete();

            Log::info('Account Type ID no: '. $accountType->id . ', has deleted!');

            return response()->json(['message' => 'Account Type has been deleted successfully!']);
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
            $accountType = AccountType::onlyTrashed()->findOrFail($id);
            $accountType->restore();

            Log::info('Account Type ID no: '. $accountType->id . ', has restored!');

            return response()->json(['message' => 'Account Type has been restored successfully!']);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 422);
        }
    }

    /**
     * Force Delete the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function forceDelete($id)
    {
        try {
            $accountType = AccountType::onlyTrashed()->findOrFail($id);
            $accountType->forceDelete();

            Log::info('Account Type ID no: '. $accountType->id . ', has force deleted!');

            return response()->json(['message' => 'Account Type has been force deleted successfully!']);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 422);
        }
    }
}
