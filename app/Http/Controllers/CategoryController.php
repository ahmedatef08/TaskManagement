<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Auth::user()->categories()
        ->withCount('tasks')
        ->orderBy('id','asc')
        ->get();
        return CategoryResource::collection($categories);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCategoryRequest $request)
    {
        $categories = Auth::user()->categories()->create([
            'name'=>$request->validated('name'),
        ]);
        return response()->json(['message'=>'Category created successfully',
                                        'data'=> new CategoryResource($categories)],
                                        201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        $this->authorize('modify',$category);
        $category->loadCount('tasks');
        return response()->json(['data' => new CategoryResource($category)]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $this->authorize('modify',$category);
        $category->update([
            'name'=>$request->validated('name'),
        ]);
        return response()->json(['message'=>'Category updated successfully',
                                        'data'=> new CategoryResource($category)],
                                        200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        $this->authorize('modify',$category);
        $category->delete();
        return response()->json(['message'=>'Category deleted successfully'],200);
    }
}
