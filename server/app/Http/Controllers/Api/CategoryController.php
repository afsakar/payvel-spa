<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Category\CategoryStoreRequest;
use App\Http\Requests\Category\CategoryUpdateRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (request()->has('all') && request()->all == 'true') {
            $categories = Category::when(request()->has('search'), function ($query) {
                $query->where('name', 'like', '%' . request()->search . '%');
            })->when(request()->has('sort'), function ($query) {
                $query->orderBy(request()->order, request()->sort);
            })->get();

            return CategoryResource::collection($categories);
        } else {
            $categories = Category::when(request()->has('search'), function ($query) {
                $query->where('name', 'like', '%' . request()->search . '%');
            })->when(request()->has('sort'), function ($query) {
                $query->orderBy(request()->order, request()->sort);
            })->fastPaginate(5);

            return CategoryResource::collection($categories);
        }
    }


    public function trash()
    {

        $deletedCategories = Category::onlyTrashed()->when(request()->has('search'), function ($query) {
            $query->where('name', 'like', '%' . request()->search . '%');
        })->when(request()->has('sort'), function ($query) {
            $query->orderBy(request()->order, request()->sort);
        })->fastPaginate(5);

        return CategoryResource::collection($deletedCategories);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CategoryStoreRequest $request)
    {
        try {
            $category = Category::create([
                'name' => $request->name,
                'type' => $request->type
            ]);

            Log::info('Category created', [
                'category' => $category
            ]);

            return response()->json(['message' => 'Category has been created successfully!']);
        } catch (\Throwable $th) {
            Log::error('Category creation failed', [
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
    public function show(Category $category)
    {
        return new CategoryResource($category);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CategoryUpdateRequest $request, Category $category)
    {
        try {
            $category->name = $request->name;
            $category->type = $request->type;
            $category->save();

            Log::info('Category updated', [
                'category' => $category
            ]);

            return response()->json(['message' => 'Category has been updated successfully!']);
        } catch (\Throwable $th) {
            Log::error('Category update failed', [
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
    public function destroy(Category $category)
    {
        try {
            $category->delete();

            Log::info('Category deleted', [
                'category' => $category
            ]);

            return response()->json(['message' => 'Category has been deleted successfully!']);
        } catch (\Throwable $th) {
            Log::error('Category deletion failed', [
                'error' => $th->getMessage()
            ]);

            return response()->json([
                'message' => $th->getMessage()
            ], 422);
        }
    }

    public function restore($id)
    {
        $category = Category::onlyTrashed()->where('id', $id)->first();
        $category->restore();

        Log::info('Category restored', [
            'category' => $category
        ]);

        return response()->json(['message' => 'Category has been restored successfully!']);
    }

    public function forceDelete($id)
    {
        $category = Category::onlyTrashed()->where('id', $id)->first();
        $category->forceDelete();

        Log::info('Category force deleted', [
            'category' => $category
        ]);

        return response()->json(['message' => 'Category has been force deleted successfully!']);
    }
}
