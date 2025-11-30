<?php

namespace App\Http\Controllers;

use App\Models\ProductImage;
Use App\Http\Resources\ProductImageResource;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProductImageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
    public function store(Request $request, Product $product)
        {
            $validator = Validator::make($request->all(), [
                'images.*' => 'required|image|max:2048',
                'is_primary' => 'nullable|boolean'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation failed.',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $uploadedImages = [];

            foreach ($request->file('images') as $image) {
                $path = $image->store('products', 'public');

                // Create the image record
                $img = ProductImage::create([
                    'product_id' => $product->id,
                    'url' => $path,
                    'is_primary' => false
                ]);

                $uploadedImages[] = $img;
            }

            // If request asks to set primary image
            if ($request->is_primary && count($uploadedImages) > 0) {
                $this->setPrimary($product, $uploadedImages[0]->id);
            }

            return response()->json([
                'message' => 'Images uploaded successfully.',
                'images' => $uploadedImages
            ], 201);
        }

    /**
     * Set a product image as primary
     */
    public function setPrimary(Product $product, $imageId)
    {
        // Ensure the image belongs to the product
        $image = $product->images()->where('id', $imageId)->firstOrFail();

        // Reset all to false
        $product->images()->update(['is_primary' => false]);

        // Set chosen image to primary
        $image->update(['is_primary' => true]);

        return response()->json([
            'message' => 'Primary image updated.',
            'primary_image' => $image
        ]);
    }
    
    /**
     * Display the specified resource.
     */
    public function show(ProductImage $productImage)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProductImage $productImage)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProductImage $productImage)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product, $imageId)
        {
            $image = $product->images()->where('id', $imageId)->firstOrFail();

            // Delete file from storage
            Storage::disk('public')->delete($image->url);

            // Delete record
            $image->delete();

            return response()->json([
                'message' => 'Image deleted successfully.'
            ]);
        }
}
