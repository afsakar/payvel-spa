<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Agreement\AgreementMediaStoreRequest;
use App\Models\Agreement;
use App\Models\Company;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class AgreementMediaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Company $company, Agreement $agreement)
    {
        return response()->json([
            'data' => $agreement->getMedia('agreements')
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AgreementMediaStoreRequest $request, Company $company, Agreement $agreement)
    {
        try {

            $name = $request['name'] != null ? $request['name'] . '.' . $request['file']->getClientOriginalExtension() : Str::random(15) . '.' . $request['file']->getClientOriginalExtension();

            $agreement->addMedia($request['file'])
                ->sanitizingFileName(function ($fileName) {
                    return strtolower(str_replace(['#', '/', '\\', ' '], '-', $fileName));
                })
                ->usingFileName($name)
                ->toMediaCollection('agreements');

            return response()->json([
                'message' => 'File uploaded successfully'
            ], 201);
        } catch (\Throwable $th) {
            Log::error('Error uploading file', [
                'error' => $th->getMessage()
            ]);

            return response()->json([
                'message' => 'Error uploading file'
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Company $company, Agreement $agreement, $media)
    {
        return $company->agreements()->findOrFail($agreement)->getMedia($media);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Company $company, Agreement $agreement, $media)
    {
        try {
            $mediaID = Media::where('uuid', $media)->first()->id;

            $agreement->deleteMedia($mediaID);

            return response()->json([
                'message' => 'File deleted successfully'
            ], 200);
        } catch (\Throwable $th) {
            Log::error('Error deleting file', [
                'error' => $th->getMessage()
            ]);

            return response()->json([
                'message' => 'Error deleting file'
            ], 500);
        }
    }
}
