<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Image;
use App\Models\Category;

class GuestController extends Controller
{
    public function index(Request $request) {
        $categories = Category::all();

        if ($request->ajax()) {
            $query = Product::with('category', 'images');

            if ($request->filled('search')) {
                $search = $request->input('search');
                $query->where('name', 'like', '%' . $search . '%');
            }

            if ($request->filled('category_id')) {
                $query->where('category_id', $request->input('category_id'));
            }

            $perPage = $request->input('per_page', 10);
            $page = $request->input('page', 1);

            $products = $query->paginate($perPage, ['*'], 'page', $page);

            return response()->json([
                'draw' => $request->input('draw'),
                'recordsTotal' => $products->total(),
                'recordsFiltered' => $products->total(),
                'data' => $products->items(),
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'total' => $products->total(),
            ]);
        }

        $products = Product::with('category', 'images')->get();
        return view('dashboard', compact('products', 'categories'));
    }

    public function view($id) {
        $product = Product::with('category', 'images')->findOrFail($id);
        return view('view', compact('product'));
    }
}
