<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Products as Product;
use App\Models\Prices;
use App\Models\Currencies;
use Illuminate\Validation\ValidationException;

/**
 * @OA\Info(
 *     title="Ciph3r-Test API",
 *     version="1.0.0",
 *     description="Documentación de la API para el proyecto Ciph3r-Test",
 *     @OA\Contact(
 *         email="soporte@ciph3r-test.com"
 *     )
 * )
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 * 
 * /**
 * @OA\Schema(
 *     schema="Product",
 *     type="object",
 *     title="Product",
 *     description="Esquema del modelo Product",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Producto de prueba"),
 *     @OA\Property(property="description", type="string", example="Descripción del producto"),
 *     @OA\Property(property="price", type="number", example=100.50),
 *     @OA\Property(property="currency_id", type="integer", example=1),
 *     @OA\Property(property="tax_cost", type="number", example=10.00),
 *     @OA\Property(property="manufacturing_cost", type="number", example=20.00),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-06-23T12:34:56Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-06-23T12:34:56Z")
 * )
 * 
 * @OA\Schema(
 *     schema="Price",
 *     type="object",
 *     title="Price",
 *     description="Esquema del modelo Price",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="product_id", type="integer", example=1),
 *     @OA\Property(property="currency_id", type="integer", example=1),
 *     @OA\Property(property="price", type="number", example=100.50),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-06-23T12:34:56Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-06-23T12:34:56Z")
 * )
 *
 */
class ProductController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/products",
     *     summary="Obtener todos los productos",
     *     tags={"Productos"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de productos",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Product")
     *         )
     *     )
     * )
     */
    public function index()
    {
        $products = Product::all();
        return response()->json($products);
    }

    /**
     * @OA\Post(
     *     path="/api/products",
     *     summary="Crear un nuevo producto",
     *     tags={"Productos"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "price", "currency_id", "tax_cost", "manufacturing_cost"},
     *             @OA\Property(property="name", type="string", example="Producto de prueba"),
     *             @OA\Property(property="description", type="string", example="Descripción del producto"),
     *             @OA\Property(property="price", type="number", example=100.50),
     *             @OA\Property(property="currency_id", type="integer", example=1),
     *             @OA\Property(property="tax_cost", type="number", example=10.00),
     *             @OA\Property(property="manufacturing_cost", type="number", example=20.00)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Producto creado exitosamente",
     *         @OA\JsonContent(ref="#/components/schemas/Product")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Errores de validación",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Validation failed"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'price' => 'required|numeric',
                'currency_id' => 'integer|exists:currencies,id',
                'tax_cost' => 'required|numeric',
                'manufacturing_cost' => 'required|numeric',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        }

        $product = Product::create($validatedData);
        return response()->json($product, 201);
    }

    /**
     * @OA\Get(
     *     path="/api/products/{id}",
     *     summary="Obtener un producto por ID",
     *     tags={"Productos"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del producto",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Detalles del producto",
     *         @OA\JsonContent(ref="#/components/schemas/Product")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Producto no encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Product not found")
     *         )
     *     )
     * )
     */
    public function show(string $id)
    {
        $product = Product::findOrFail($id);
        return response()->json($product);
    }

    /**
     * @OA\Put(
     *     path="/api/products/{id}/edit",
     *     summary="Actualizar un producto por ID",
     *     tags={"Productos"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del producto",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "price", "currency_id", "tax_cost", "manufacturing_cost"},
     *             @OA\Property(property="name", type="string", example="Producto actualizado"),
     *             @OA\Property(property="description", type="string", example="Descripción actualizada"),
     *             @OA\Property(property="price", type="number", example=150.00),
     *             @OA\Property(property="currency_id", type="integer", example=1),
     *             @OA\Property(property="tax_cost", type="number", example=15.00),
     *             @OA\Property(property="manufacturing_cost", type="number", example=25.00)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Producto actualizado exitosamente",
     *         @OA\JsonContent(ref="#/components/schemas/Product")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Errores de validación",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Validation failed"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function update(Request $request, string $id)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'price' => 'required|numeric',
                'currency_id' => 'integer|exists:currencies,id',
                'tax_cost' => 'required|numeric',
                'manufacturing_cost' => 'required|numeric',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        }

        $product = Product::findOrFail($id);
        $product->update($validatedData);

        return response()->json($product);
    }

    /**
     * @OA\Delete(
     *     path="/api/products/{id}",
     *     summary="Eliminar un producto por ID",
     *     tags={"Productos"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del producto",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Producto eliminado exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Product deleted successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Producto no encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Product not found")
     *         )
     *     )
     * )
     */
    public function destroy(string $id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return response()->json(['message' => 'Product deleted successfully']);
    }

        /**
     * @OA\Get(
     *     path="/api/products/{id}/prices",
     *     summary="Obtener los precios de un producto",
     *     tags={"Productos"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del producto",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de precios del producto",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Price")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No se encontraron precios para el producto",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="No prices found for this product")
     *         )
     *     )
     * )
     */
    public function getProductPrices(Request $request, string $id)
    {
        $prices = Prices::where('product_id', $id)->get();
        if ($prices->isEmpty()) {
            return response()->json(['message' => 'No prices found for this product'], 404);
        }
        return response()->json($prices);
    }

    /**
     * @OA\Post(
     *     path="/api/products/{id}/prices",
     *     summary="Agregar un precio a un producto",
     *     tags={"Productos"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del producto",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"currency_id"},
     *             @OA\Property(property="currency_id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Precio agregado exitosamente",
     *         @OA\JsonContent(ref="#/components/schemas/Price")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Producto no encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Product not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="El precio ya existe para este producto y moneda",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Price already exists for this product and currency")
     *         )
     *     )
     * )
     */
    public function addPrice(Request $request, string $id)
    {
        $validatedData = $request->validate([
            'currency_id' => 'integer|exists:currencies,id',
        ], [
            'currency_id.integer' => 'El ID de la moneda debe ser un número entero.',
            'currency_id.exists' => 'La moneda especificada no existe en la base de datos.',
        ]);

        $product = Product::findOrFail($id);
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

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
