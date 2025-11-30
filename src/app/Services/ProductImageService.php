<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductImageService
{
    /**
     * Upload multiple images
     */
    public function uploadImages(Product $product, Request $request)
    {
        $uploaded = [];

        foreach ($request->file('images') as $image) {
            $path = $image->store('products', 'public');

            $img = ProductImage::create([
                'product_id' => $product->id,
                'url' => $path,
                'is_primary' => false
            ]);

            $uploaded[] = $img;
        }

        // If a primary image is requested
        if ($request->is_primary && count($uploaded) > 0) {
            $this->setPrimaryImage($product, $uploaded[0]->id);
        }

        return [
            'message' => 'Images uploaded successfully.',
            'images' => $uploaded
        ];
    }

    /**
     * Set one image as primary
     */
    public function setPrimaryImage(Product $product, $imageId)
    {
        $image = $product->images()->where('id', $imageId)->firstOrFail();

        // Reset all images
        $product->images()->update(['is_primary' => false]);

        // Set this one as primary
        $image->update(['is_primary' => true]);

        return [
            'message' => 'Primary image updated.',
            'primary_image' => $image
        ];
    }

    /**
     * Delete an image
     */
    public function deleteImage(Product $product, $imageId)
    {
        $image = $product->images()->where('id', $imageId)->firstOrFail();

        // Delete file from storage
        Storage::disk('public')->delete($image->url);

        $image->delete();

        return [
            'message' => 'Image deleted successfully.'
        ];
    }
}
