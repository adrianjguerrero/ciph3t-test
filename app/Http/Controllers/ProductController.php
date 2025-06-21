<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Products as Product;
use App\Models\Prices;
use App\Models\Currencies;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::all();
        return response()->json($products);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'currency_id' => 'integer|exists:currencies,id',
            'tax_cost' => 'required|numeric',
            'manufacturing_cost' => 'required|numeric',
        ]);

        $product = Product::create($validatedData);
        
        
        return response()->json($product, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = Product::findOrFail($id);
        return response()->json($product);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'currency_id' => 'integer|exists:currencies,id',
            'tax_cost' => 'required|numeric',
            'manufacturing_cost' => 'required|numeric',
        ]);

        $product = Product::findOrFail($id);
        $product->update($validatedData);

        return response()->json($product);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return response()->json(['message' => 'Product deleted successfully']);
    }

    public function getProductPrices(Request $request, string $id)
    {
        $prices = Prices::where('product_id', $id)->get();
        if ($prices->isEmpty()) {
            return response()->json(['message' => 'No prices found for this product'], 404);
        }
        return response()->json($prices);

        
    }

    public function addPrice(Request $request, string $id)
    {
        $validatedData = $request->validate([
            'currency_id' => 'integer|exists:currencies,id',
        ]);
        $product = Product::findOrFail($id);
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }
        // Check if the price already exists for the product and currency
        $existingPrice = Prices::where('product_id', $id)
            ->where('currency_id', $validatedData['currency_id'])
            ->first();
        if ($existingPrice) {
            return response()->json(['message' => 'Price already exists for this product and currency'], 409);
        }

        $manufacturingCost = $product->manufacturing_cost;
        $taxCost = $product->tax_cost;
        $productPrice = $product->price;
        $basePrice = ($manufacturingCost + $taxCost + $productPrice);
        $exchangeRate = Currencies::find($validatedData['currency_id'])->exchange_rate;
        $priceToSave  = $basePrice * $exchangeRate;
        $validatedData['price'] = $priceToSave;
        $validatedData['product_id'] = $id;
        $validatedData['currency_id'] = $validatedData['currency_id'];

        $price = new Prices($validatedData);
        $price->product_id = $id;
        $price->save();

        return response()->json($price, 201);
    }
}
