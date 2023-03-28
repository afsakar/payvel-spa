<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Account\AccountStoreRequest;
use App\Http\Requests\Account\AccountUpdateRequest;
use App\Http\Resources\AccountResource;
use Illuminate\Http\Request;
use App\Models\Account;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AccountController extends Controller
{

    /*

     public function index()
    {
        $accounts = $this->optimizeQuery(Account::class, [], '-accounts', 5);

        return AccountResource::collection($accounts);
    }

    */
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (request()->has('all') && request()->all == 'true') {
            $accounts = Account::when(request()->has('search'), function ($query) {
                $query->where('name', 'like', '%' . request()->search . '%');
            })->when(request()->has('sort'), function ($query) {
                $query->orderBy(request()->order, request()->sort);
            })->get();

            return AccountResource::collection($accounts);
        } else {
            $accounts = Account::when(request()->has('search'), function ($query) {
                $query->where('name', 'like', '%' . request()->search . '%');
            })->when(request()->has('sort'), function ($query) {
                $query->orderBy(request()->order, request()->sort);
            })->fastPaginate(5);

            return AccountResource::collection($accounts);
        }
    }

    public function trash()
    {
        $deletedAccounts = Account::onlyTrashed()->when(request()->has('search'), function ($query) {
            $query->where('name', 'like', '%' . request()->search . '%');
        })->when(request()->has('sort'), function ($query) {
            $query->orderBy(request()->order, request()->sort);
        })->fastPaginate(5);

        return AccountResource::collection($deletedAccounts);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AccountStoreRequest $request)
    {
        try {
            DB::transaction(function () use ($request) {
                Account::create($request->validated());

                Log::info('New Account created!');

                return response()->json(['message' => 'Account has been created successfully!']);
            });
        } catch (\Throwable $th) {
            Log::error('Error creating account', [
                'error' => $th->getMessage()
            ]);

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
    public function show(Account $account)
    {
        return new AccountResource($account);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(AccountUpdateRequest $request, Account $account)
    {
        try {
            DB::transaction(function () use ($request, $account) {
                $account->update($request->validated());

                Log::info('Account ID no: ' . $account->id . ', has updated!');

                return response()->json(['message' => 'Account has been updated successfully!']);
            });
        } catch (\Throwable $th) {
            Log::error('Error updating account', [
                'error' => $th->getMessage()
            ]);

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
    public function destroy(Account $account)
    {
        try {
            DB::transaction(function () use ($account) {
                $account->delete();

                Log::info('Account ID no: ' . $account->id . ', has deleted!');

                return response()->json(['message' => 'Account has been deleted successfully!']);
            });
        } catch (\Throwable $th) {
            Log::error('Error creating account', [
                'error' => $th->getMessage()
            ]);

            return response()->json([
                'message' => $th->getMessage()
            ], 422);
        }
    }


    /**
     *
     * @param int $id
     */
    public function restore($id)
    {
        $account = Account::onlyTrashed()->where('id', $id)->first();
        $account->restore();
        Log::info('Account ID no: ' . $account->id . ', has restored!');
        return response()->json(['message' => 'Account has been restored successfully!']);
    }

    /**
     *
     * @param int $id
     */
    public function forceDelete($id)
    {
        try {
            $account = Account::onlyTrashed()->where('id', $id)->first();
            $account->forceDelete();

            Log::info('Account ID no: ' . $account->id . ', has force deleted!');

            return response()->json(['message' => 'Account has been deleted successfully!']);
        } catch (\Throwable $th) {
            Log::error('Error deleting account', [
                'error' => $th->getMessage()
            ]);

            return response()->json([
                'message' => $th->getMessage()
            ], 422);
        }
    }
}
