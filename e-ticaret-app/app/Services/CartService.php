<?php

namespace App\Services;

use Illuminate\Support\Facades\Redis;
use App\Models\Product;

class CartService
{
    /**
     * Sepete ürün ekle
     */
    public function addToCart($userId, $productId, $quantity = 1)
    {
        $key = "cart:{$userId}";
        
        // Ürün bilgilerini al
        $product = Product::find($productId);
        if (!$product) {
            return false;
        }
        
        // Stok kontrolü
        $stockService = new StockService();
        if ($stockService->getStock($productId) < $quantity) {
            return false;
        }
        
        // Sepetteki mevcut ürün miktarını al
        $currentQuantity = $this->getCartItemQuantity($userId, $productId);
        $newQuantity = $currentQuantity + $quantity;
        
        // Sepete ekle/güncelle
        $cartItem = [
            'id' => $productId,
            'name' => $product->name,
            'price' => $product->price,
            'quantity' => $newQuantity,
            'image' => $product->image,
        ];
        
        Redis::hset($key, $productId, json_encode($cartItem));
        Redis::expire($key, 86400); // 24 saat
        
        return $cartItem;
    }

    /**
     * Sepetten ürün çıkar
     */
    public function removeFromCart($userId, $productId)
    {
        $key = "cart:{$userId}";
        return Redis::hdel($key, $productId);
    }

    /**
     * Sepetteki ürün miktarını güncelle
     */
    public function updateCartItem($userId, $productId, $quantity)
    {
        if ($quantity <= 0) {
            return $this->removeFromCart($userId, $productId);
        }
        
        $key = "cart:{$userId}";
        $cartItem = $this->getCartItem($userId, $productId);
        
        if (!$cartItem) {
            return false;
        }
        
        // Stok kontrolü
        $stockService = new StockService();
        if ($stockService->getStock($productId) < $quantity) {
            return false;
        }
        
        $cartItem['quantity'] = $quantity;
        Redis::hset($key, $productId, json_encode($cartItem));
        
        return $cartItem;
    }

    /**
     * Sepetteki ürün bilgisini al
     */
    public function getCartItem($userId, $productId)
    {
        $key = "cart:{$userId}";
        $item = Redis::hget($key, $productId);
        
        return $item ? json_decode($item, true) : null;
    }

    /**
     * Sepetteki ürün miktarını al
     */
    public function getCartItemQuantity($userId, $productId)
    {
        $item = $this->getCartItem($userId, $productId);
        return $item ? $item['quantity'] : 0;
    }

    /**
     * Kullanıcının sepetini al
     */
    public function getCart($userId)
    {
        $key = "cart:{$userId}";
        $cart = Redis::hgetall($key);
        
        $items = [];
        foreach ($cart as $productId => $itemData) {
            $items[$productId] = json_decode($itemData, true);
        }
        
        return $items;
    }

    /**
     * Sepet toplam tutarını hesapla
     */
    public function getCartTotal($userId)
    {
        $cart = $this->getCart($userId);
        $total = 0;
        
        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }
        
        return $total;
    }

    /**
     * Sepeti temizle
     */
    public function clearCart($userId)
    {
        $key = "cart:{$userId}";
        return Redis::del($key);
    }

    /**
     * Sepetteki ürün sayısını al
     */
    public function getCartItemCount($userId)
    {
        $key = "cart:{$userId}";
        return Redis::hlen($key);
    }
}
