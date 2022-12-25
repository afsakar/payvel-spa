<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Company\CompanyStoreRequest;
use App\Http\Requests\Company\CompanyUpdateRequest;
use App\Http\Resources\CompanyResource;
use App\Models\Company;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return AnonymousResourceCollection
     */
    public function index()
    {
        $companies = Company::all();
        $deletedCompanies = Company::onlyTrashed()->get();

        return CompanyResource::collection($companies)->additional([
            'deletedCompanies' => $deletedCompanies
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CompanyStoreRequest $request
     * @return JsonResponse
     */
    public function store(CompanyStoreRequest $request)
    {
        try {
            DB::transaction(function () use ($request) {
                $image = null;

                if ($request->hasFile('logo')) {
                    $image = $request->file('logo');
                    $imageName = Str::random() . '.' . $image->getClientOriginalExtension();
                    $image->move(public_path('images/company-logos'), $imageName);
                    $image = '/images/company-logos/' . $imageName;
                }

                Company::create([
                    'logo' => $image ?? null,
                    'name' => $request->name,
                    'owner' => $request->owner,
                    'tel_number' => $request->tel_number,
                    'gsm_number' => $request->gsm_number,
                    'fax_number' => $request->fax_number,
                    'email' => $request->email,
                    'address' => $request->address,
                    'tax_office' => $request->tax_office,
                    'tax_number' => $request->tax_number,
                ]);

                Log::info('New Company created!');

                return response()->json(['message' => 'Company has been created successfully!']);
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
     * @param Company $company
     * @return CompanyResource
     */
    public function show(Company $company)
    {
        return new CompanyResource($company);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param CompanyUpdateRequest $request
     * @param Company $company
     * @return JsonResponse
     */
    public function update(CompanyUpdateRequest $request, Company $company)
    {
        try {
            DB::transaction(function () use ($company, $request) {
                $image = $company->logo ?? null;

                if ($request->hasFile('logo') && $request->file('logo') !== null) {
                    $image = $request->file('logo');
                    $imageName = Str::random() . '.' . $image->getClientOriginalExtension();
                    $image->move(public_path('images/company-logos'), $imageName);
                    File::delete(public_path($company->logo));
                    $image = '/images/company-logos/' . $imageName;
                    $company->logo = $image;
                }
                    $company->name = $request->name;
                    $company->owner = $request->owner;
                    $company->tel_number = $request->tel_number;
                    $company->gsm_number = $request->gsm_number;
                    $company->fax_number = $request->fax_number;
                    $company->email = $request->email;
                    $company->address = $request->address;
                    $company->tax_office = $request->tax_office;
                    $company->tax_number = $request->tax_number;
                    $company->save();

                Log::info('Company ID no: '. $company->id . ', has updated!');

                return response()->json(['message' => 'Company has been updated successfully!']);
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
     * @param Company $company
     * @return JsonResponse
     */
    public function destroy(Company $company)
    {
        $company->delete();
        return response()->json(['message' => 'Company has been deleted successfully!']);
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function restore($id)
    {
        $company = Company::onlyTrashed()->where('id', $id)->first();
        $company->restore();
        return response()->json(['message' => 'Company has been restored successfully!']);
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function forceDelete($id)
    {
        $company = Company::onlyTrashed()->where('id', $id)->first();
        if($company->logo !== null) {
            File::delete(public_path($company->logo));
        }
        $company->forceDelete();
        return response()->json(['message' => 'Company has been deleted successfully!']);
    }
}
