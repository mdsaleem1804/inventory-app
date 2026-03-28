<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard', [
            'totalProducts' => Product::count(),
            'activeProducts' => Product::where('is_active', true)->count(),
            'totalCategories' => Category::count(),
        ]);
    }
}
