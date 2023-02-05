<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Agreement\AgreementStoreRequest;
use App\Http\Requests\Agreement\AgreementUpdateRequest;
use App\Http\Resources\AgreementResource;
use App\Models\Agreement;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AgreementController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Company $company)
    {
        $agreement = $company->agreements()->with('corporation');
        return AgreementResource::collection($agreement->get())->additional([
            'deleted_agreements' => $agreement->onlyTrashed()->get()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AgreementStoreRequest $request)
    {
        try {
            DB::transaction(function () use ($request) {
                $agreement = Agreement::create($request->validated());

                Log::info('Agreement created successfully', [
                    'agreement' => $agreement,
                ]);

                return response()->json([
                    'message' => 'Agreement created successfully'
                ], 201);
            });
        } catch (\Throwable $th) {
            Log::error('Error creating agreement', [
                'error' => $th->getMessage()
            ]);

            return response()->json([
                'message' => 'Error creating agreement'
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($agreement)
    {
        $agreement = Agreement::with('company', 'corporation', 'media')->where('id', $agreement)->first();
        return new AgreementResource($agreement);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(AgreementUpdateRequest $request, Agreement $agreement)
    {
        try {
            DB::transaction(function () use ($request, $agreement) {
                $agreement->update($request->validated());

                Log::info('Agreement updated successfully', [
                    'agreement' => $agreement,
                ]);

                return response()->json([
                    'message' => 'Agreement updated successfully'
                ], 201);
            });
        } catch (\Throwable $th) {
            Log::error('Error updating agreement', [
                'error' => $th->getMessage()
            ]);

            return response()->json([
                'message' => 'Error updating agreement'
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Agreement $agreement)
    {
        try {
            $agreement->delete();

            Log::info('Agreement deleted successfully', [
                'agreement' => $agreement,
            ]);

            return response()->json([
                'message' => 'Agreement deleted successfully'
            ], 201);
        } catch (\Throwable $th) {
            Log::error('Error deleting agreement', [
                'error' => $th->getMessage()
            ]);

            return response()->json([
                'message' => 'Error deleting agreement'
            ], 500);
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
            $agreement = Agreement::onlyTrashed()->where('id', $id)->first();
            $agreement->restore();

            Log::info('Agreement restored successfully', [
                'agreement' => $agreement,
            ]);

            return response()->json([
                'message' => 'Agreement restored successfully'
            ], 201);
        } catch (\Throwable $th) {
            Log::error('Error restoring agreement', [
                'error' => $th->getMessage()
            ]);

            return response()->json([
                'message' => 'Error restoring agreement'
            ], 500);
        }
    }

    /**
     * Force delete the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function forceDelete($id)
    {
        try {
            $agreement = Agreement::onlyTrashed()->where('id', $id)->first();
            // $agreement->deletePreservingMedia();
            $agreement->forceDelete();

            Log::info('Agreement force deleted successfully', [
                'agreement' => $agreement,
            ]);

            return response()->json([
                'message' => 'Agreement force deleted successfully'
            ], 201);
        } catch (\Throwable $th) {
            Log::error('Error force deleting agreement', [
                'error' => $th->getMessage()
            ]);

            return response()->json([
                'message' => 'Error force deleting agreement'
            ], 500);
        }
    }

    public function get_files(Agreement $agreement)
    {
        $files = $agreement->getMedia();

        return response()->json([
            'files' => $files
        ], 200);
    }

    public function upload_file(Request $request, Agreement $agreement)
    {
       try {
            $file = $request->file('file');
            $fileName = Str::random(10) . '.' . $file->getClientOriginalExtension();
            $agreement->addMedia($file)->usingFileName($fileName)->toMediaCollection('agreements');

            Log::info('Agreement file uploaded successfully', [
                'agreement' => $agreement,
            ]);

            return response()->json([
                'message' => 'Agreement file uploaded successfully'
            ], 201);
       } catch (\Throwable $th) {
            Log::error('Error uploading agreement file', [
                'error' => $th->getMessage()
            ]);

            return response()->json([
                'message' => 'Error uploading agreement file'
            ], 500);
       }
    }

    public function download_file(Agreement $agreement, $id)
    {
        $file = $agreement->getMedia()->where('id', $id)->first();

        return response()->download($file->getPath(), $file->file_name);
    }

    public function delete_file(Agreement $agreement, $id)
    {
        try {
            $file = $agreement->getMedia()->where('id', $id)->first();
            $file->delete();

            Log::info('Agreement file deleted successfully', [
                'agreement' => $agreement,
            ]);

            return response()->json([
                'message' => 'Agreement file deleted successfully'
            ], 201);
        } catch (\Throwable $th) {
            Log::error('Error deleting agreement file', [
                'error' => $th->getMessage()
            ]);

            return response()->json([
                'message' => 'Error deleting agreement file'
            ], 500);
        }
    }
}
