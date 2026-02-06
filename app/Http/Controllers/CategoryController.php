<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Auth as FacadesAuth;

class CategoryController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::where('user_id',Auth::id())
        ->withCount('tasks')
        ->orderBy('id','asc')
        ->get();
        return response()->json(['data' => $categories]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCategoryRequest $request)
    {
        $categories = Category::create([
            'name'=>$request->validated('name'),
            'user_id'=>Auth::id(),
        ]);
        return response()->json(['message'=>'Category created successfully',
                                        'Categories'=> $categories],
                                        201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        $this->authorize('modify',$category);
        $category->loadCount('tasks');
        return response()->json(['data' => $category]);
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
                                        'Category'=> $category],
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
