<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Revenue\RevenueStoreRequest;
use App\Http\Requests\Revenue\RevenueUpdateRequest;
use App\Http\Resources\RevenueResource;
use App\Models\Company;
use Illuminate\Http\Request;

class RevenueController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        $company = Company::withTrashed()->findOrFail($id);

        $revenues = $company->revenues()->with(['account', 'category', 'invoices', 'corporation']);

        if (request()->has('all') && request()->all == 'true') {
            // get all revenues
            $revenueList = $revenues->when(request()->has('min_date'), function ($query) {
                if (request()->has('max_date')) {
                    return $query->whereBetween('due_at', [request()->min_date, request()->max_date]);
                } else {
                    return $query->where('due_at', '>=', request()->min_date);
                }
            })->get();
        } else {
            // get paginated revenues
            $revenueList = $revenues->when(request()->has('min_date'), function ($query) {
                if (request()->has('max_date')) {
                    return $query->whereBetween('due_at', [request()->min_date, request()->max_date]);
                } else {
                    return $query->where('due_at', '>=', request()->min_date);
                }
            })->fastPaginate(5);

            return RevenueResource::collection($revenueList);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(RevenueStoreRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(RevenueUpdateRequest $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
