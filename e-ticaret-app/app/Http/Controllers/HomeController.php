<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Services\StockService;

class HomeController extends Controller
{
    public function index()
    {
        $products = Product::with('category')
            ->where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->take(12)
            ->get();

        $categories = Category::where('is_active', true)->get();

        return view('home', compact('products', 'categories'));
    }

    public function products()
    {
        $products = Product::with('category')
            ->where('is_active', true)
            ->paginate(20);

        return view('products', compact('products'));
    }

    public function product($id)
    {
        $product = Product::with('category')->findOrFail($id);
        
        // Redis'ten stok bilgisini al
        $stockService = new StockService();
        $currentStock = $stockService->getStock($id);
        
        return view('product', compact('product', 'currentStock'));
    }
}
