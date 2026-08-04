<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;

class ProductController
{
    /**
     * Products index
     *
     * @return \Inertia\Response
     */
    public function index(Request $request): Response
    {
        $filters = $request->only(['product_name', 'product_description', 'quantity', 'price']);

        if (array_filter($filters)) {
            return Inertia::render('Product/Index', [
                'products' => Product::all(),
                'summary' => Inertia::defer(fn() => $filters),
            ]);
        }

        return Inertia::render('Product/Index', [
            'products' => Product::all(),
        ]);
    }

    /**
     * Store a new product.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_name' => 'required|string|max:255',
            'product_description' => 'required|string',
            'quantity' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
        ]);

        Product::create([
            ...$validated,
            'status' => 'draft',
        ]);

        return back();
    }

    /**
     * Update an existing product.
     */
    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'product_name' => 'required|string|max:255',
            'product_description' => 'required|string',
            'quantity' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
        ]);

        $product->update($validated);

        return back();
    }

    /**
     * Delete a product.
     */
    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();

        return to_route('products.index');
    }

    /**
     * Disable a product.
     */
    public function disable(Product $product): RedirectResponse
    {
        $product->update(['status' => 'archived']);

        return back();
    }

    /**
     * Enable a product.
     */
    public function enable(Product $product): RedirectResponse
    {
        $product->update(['status' => 'published']);

        return back();
    }
}
