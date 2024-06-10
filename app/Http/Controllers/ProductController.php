<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function __construct()
    {
        // $this->middleware(['auth:sanctum', 'ability:show-product'])->only(['index', 'show']);
        $this->middleware(['auth:sanctum', 'ability:delete-product'])->only(['destroy']);
        $this->middleware(['auth:sanctum', 'ability:update-product'])->only(['update']);
        $this->middleware(['auth:sanctum', 'ability:store-product'])->only(['store']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // $this->middleware('auth:sanctum');

        $data = Product::all();

        return response()->json([
            'status' => true,
            'total' => $data->count(),
            'data' => $data
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->only(['name', 'count', 'price']);

        $rules = [
            'name' => 'required|min:3',
            'count' => 'required|integer',
            'price' => 'required|integer'
        ];

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'data' => $validator->errors()
            ]);
        } else {
            Product::create($data);

            return response()->json([
                'status' => true,
                'data' => 'data saved successfully'
            ]);
        };
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        try {
            $data = Product::findOrFail($product->id);
            return response()->json([
                'status' => true,
                'data' => $data
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'data' => 'data not found'
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $data = $request->only(['name', 'count', 'price']);

        $rules = [
            'name' => 'min:3',
            'count' => 'integer',
            'price' => 'integer'
        ];

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'data' => $validator->fails()
            ]);
        } else {
            $data = Product::find($product->id)->update($data);

            return response()->json([
                'status' => true,
                'data' => 'data successfully updated',
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $product->delete();

        return response()->json([
            'status' => true,
            'data' => 'deleted successfully'
        ], 200);
    }
}
