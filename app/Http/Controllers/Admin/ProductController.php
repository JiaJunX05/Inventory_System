<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Image;
use App\Models\Category;

class ProductController extends Controller
{
    public function index(Request $request) {
        $categories = Category::all();

        if ($request->ajax()) {
            $query = Product::with('category', 'images');

            if ($request->filled('search')) {
                $search = $request->input('search');
                $query->where('name', 'like', "%{$search}%");
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
        return view('admin.dashboard', compact('products', 'categories'));
    }

    public function showCreateForm() {
        $categories = Category::all();
        return view('admin.create', compact('categories'));
    }

    public function create(Request $request) {
        $request->validate([
            'feature' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'name' => 'required|string|max:255|unique:products,name',
            'description' => 'nullable|string|max:1000',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'image.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('feature')) {
            $feature = $request->file('feature');
            $featureName = time() . '.' . $feature->getClientOriginalExtension();
            $feature->move(public_path('assets/features'), $featureName);
        }

        $products = Product::create([
            'feature' => 'features/' . $featureName,
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'quantity' => $request->quantity,
            'category_id' => $request->category_id,
        ]);

        if ($request->hasFile('image')) {
            foreach ($request->file('image') as $imageFile) {
                $imageName = time() . uniqid() . '.' . $imageFile->getClientOriginalExtension();
                $imageFile->move(public_path('assets/images'), $imageName);

                Image::create([
                    'image' => 'images/' . $imageName,
                    'product_id' => $products->id,
                ]);
            }
        } else {
            return redirect()->back()->withErrors(['image' => 'No image uploaded.']);
        }

        return redirect()->route('admin.dashboard')->with('success', 'Product created successfully.');
    }

    public function view($id) {
        $product = Product::with('category', 'images')->findOrFail($id);
        return view('admin.view', compact('product'));
    }

    public function showEditForm($id) {
        $product = Product::with('category', 'images')->findOrFail($id);
        $categories = Category::all();
        return view('admin.update', compact('product', 'categories'));
    }

    public function update(Request $request, $id) {
        $request->validate([
            'feature' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'name' => 'required|string|max:255|unique:products,name,' . $id,
            'description' => 'nullable|string|max:1000',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'category_id' => 'nullable|exists:categories,id',
            'image.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $products = Product::findOrFail($id);

        if ($request->hasFile('feature')) {
            if ($products->feature && file_exists(public_path('assets/' . $products->feature))) {
                unlink(public_path('assets/' . $products->feature));
            }

            $featureName = time() . '.' . $request->file('feature')->getClientOriginalExtension();
            $request->file('feature')->move(public_path('assets/features'), $featureName);
            $products->feature = 'features/' . $featureName;
        }

        $products->update([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'quantity' => $request->quantity,
            'category_id' => $request->category_id,
        ]);

        // 删除选中的图片
        if ($request->has('remove_image')) {
            foreach ($request->remove_image as $imageId) {
                $image = Image::find($imageId);
                if ($image) {
                    // 删除文件
                    if (file_exists(public_path('assets/' . $image->image))) {
                        unlink(public_path('assets/' . $image->image));
                    }
                    // 删除数据库记录
                    $image->delete();
                }
            }
        }

        // 添加新的图片
        if ($request->hasFile('image')) {
            foreach ($request->file('image') as $imageFile) {
                $imageName = time() . uniqid() . '.' . $imageFile->getClientOriginalExtension();
                $imageFile->move(public_path('assets/images'), $imageName);

                Image::create([
                    'image' => 'images/' . $imageName,
                    'product_id' => $products->id,
                ]);
            }
        }

        return redirect()->route('product.view', $id)->with('success', 'Product updated successfully.');
    }

    public function showStockForm($id) {
        $product = Product::find($id);
        return view('admin.stock', compact('product'));
    }

    public function stockUpdate(Request $request, $id) {
        $product = Product::find($id);

        $request->validate([
            'stock_quantity' => 'required|integer|min:1',
            'status' => 'required|in:stock_in,stock_out',
        ]);

        $stock_quantity = $request->stock_quantity;
        $status = $request->status;

        if ($status === 'stock_in') {
            $product->quantity += $stock_quantity;
        } elseif ($status === 'stock_out') {
            if ($product->quantity < $stock_quantity) {
                return back()->withErrors('Stock quantity exceeds available stock.');
            }
            $product->quantity -= $stock_quantity;
        }

        $product->save();

        return redirect()->route('product.view', $id)->with('success', 'Stock updated successfully.');
    }

    public function destroy($id) {
        $products = Product::findOrFail($id);

        if ($products->feature && file_exists(public_path('assets/' . $products->feature))) {
            unlink(public_path('assets/' . $products->feature));
        }

        $images = Image::where('product_id', $products->id)->get();
        foreach ($images as $image) {
            if (file_exists(public_path('assets/' . $image->image))) {
                unlink(public_path('assets/' . $image->image));
            }
            $image->delete();
        }

        $products->delete();

        return redirect()->route('admin.dashboard')->with('success', 'Product deleted successfully.');
    }
}
