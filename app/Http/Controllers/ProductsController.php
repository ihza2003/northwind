<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductsController extends Controller
{
    /**
     * Endpoint 1: GET /api/products
     */
    public function index(Request $request)
    {
        // Menggunakan singular 'category' dan 'supplier' (asumsi nama relasi di Model)
        $query = Product::with(['categories', 'suppliers']);

        $query->when($request->category_id, fn($q) => $q->where('category_id', $request->category_id))
            ->when($request->supplier_id, fn($q) => $q->where('supplier_id', $request->supplier_id));

        $query->when($request->min_price, fn($q) => $q->where('unit_price', '>=', $request->min_price))
            ->when($request->max_price, fn($q) => $q->where('unit_price', '<=', $request->max_price));

        if ($request->filled('search')) {
            $query->whereRaw('LOWER(product_name) LIKE ?', ['%' . strtolower($request->search) . '%']);
        }

        $sort = $request->get('sort', 'product_name:asc');
        $parts = explode(':', $sort);
        $sortBy = $parts[0] ?? 'product_name';
        $sortOrder = $parts[1] ?? 'asc';

        $allowedSort = ['product_name', 'unit_price', 'units_in_stock'];
        if (in_array($sortBy, $allowedSort)) {
            $query->orderBy($sortBy, in_array($sortOrder, ['asc', 'desc']) ? $sortOrder : 'asc');
        }

        $products = $query->paginate($request->get('limit', 10));

        // Format output api
        $products->getCollection()->transform(function ($p) {
            return [
                'product_id'     => $p->product_id,
                'product_name'   => $p->product_name,
                'unit_price'     => (float) $p->unit_price,
                'units_in_stock' => $p->units_in_stock,
                'category_name'  => $p->category->category_name ?? null,
                'supplier_name'  => $p->supplier->company_name ?? null,
            ];
        });

        return response()->json([
            'message' => 'Success',
            'status'  => 200,
            'data'    => $products->items(),
            'meta'    => [
                'pagination' => [
                    'page'        => $products->currentPage(),
                    'total'       => $products->total(),
                    'total_pages' => $products->lastPage(),
                ],
                'sort' => "$sortBy:$sortOrder"
            ]
        ]);
    }

    /**
     * Endpoint 2: GET /api/products/:id
     */
    public function show($id)
    {
        $product = Product::with(['categorie', 'suppliers'])
            ->withSum('orderDetails as total terjual', 'quantity')
            ->find($id);

        return response()->json([
            'status' => 200,
            'data'   => [
                'product_id'    => $product->product_id,
                'product_name'  => $product->product_name,
                'category_name' => $product->category->category_name ?? null,
                'supplier_name' => $product->supplier->company_name ?? null,
                'unit_price'    => (float) $product->unit_price,
                'total_terjual' => (int) ($product->total_terjual ?? 0)
            ]
        ]);
    }

    /**
     * Endpoint 3: POST /api/products
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_name'   => 'required|min:3',
            'supplier_id'    => 'required|exists:suppliers,supplier_id',
            'category_id'    => 'required|exists:categories,category_id',
            'unit_price'     => 'required|numeric|gt:0',
            'units_in_stock' => 'nullable|integer|min:0',
            'discontinued'   => 'nullable|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $product = Product::create([
            'product_name'   => $request->product_name,
            'supplier_id'    => $request->supplier_id,
            'category_id'    => $request->category_id,
            'unit_price'     => $request->unit_price,
            'units_in_stock' => $request->get('units_in_stock', 0),
            'discontinued'   => $request->get('discontinued', false) ? 1 : 0,
        ]);

        return response()->json([
            'message' => 'Product created successfully',
            'data'    => $product
        ], 201);
    }
}
