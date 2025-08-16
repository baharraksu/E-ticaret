<?php

namespace App\Services;

use Illuminate\Support\Facades\Redis;
use App\Models\Product;

class StockService
{
    /**
     * Ürün stok miktarını Redis'ten al
     */
    public function getStock($productId)
    {
        $key = "product:stock:{$productId}";
        $stock = Redis::get($key);
        
        if ($stock === null) {
            // Redis'te yoksa veritabanından al ve cache'le
            $product = Product::find($productId);
            if ($product) {
                $this->setStock($productId, $product->stock);
                return $product->stock;
            }
            return 0;
        }
        
        return (int) $stock;
    }

    /**
     * Ürün stok miktarını Redis'e kaydet
     */
    public function setStock($productId, $stock)
    {
        $key = "product:stock:{$productId}";
        Redis::set($key, $stock);
        
        // 1 saat cache'de tut
        Redis::expire($key, 3600);
    }

    /**
     * Stok miktarını azalt (satış sırasında)
     */
    public function decreaseStock($productId, $quantity = 1)
    {
        $key = "product:stock:{$productId}";
        $currentStock = $this->getStock($productId);
        
        if ($currentStock >= $quantity) {
            $newStock = $currentStock - $quantity;
            $this->setStock($productId, $newStock);
            
            // Veritabanını da güncelle
            Product::where('id', $productId)->update(['stock' => $newStock]);
            
            return $newStock;
        }
        
        return false; // Yetersiz stok
    }

    /**
     * Stok miktarını artır (iade sırasında)
     */
    public function increaseStock($productId, $quantity = 1)
    {
        $key = "product:stock:{$productId}";
        $currentStock = $this->getStock($productId);
        $newStock = $currentStock + $quantity;
        
        $this->setStock($productId, $newStock);
        
        // Veritabanını da güncelle
        Product::where('id', $productId)->update(['stock' => $newStock]);
        
        return $newStock;
    }

    /**
     * Tüm ürünlerin stok durumunu Redis'e yükle
     */
    public function loadAllStocks()
    {
        $products = Product::all();
        
        foreach ($products as $product) {
            $this->setStock($product->id, $product->stock);
        }
    }

    /**
     * Stok uyarısı (düşük stok)
     */
    public function getLowStockProducts($threshold = 10)
    {
        $products = Product::where('stock', '<=', $threshold)->get();
        
        foreach ($products as $product) {
            $key = "product:low_stock:{$product->id}";
            Redis::set($key, $product->stock);
            Redis::expire($key, 1800); // 30 dakika
        }
        
        return $products;
    }
}
