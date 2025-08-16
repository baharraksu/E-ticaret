<?php

namespace App\Http\Controllers;

use App\Services\MongoDBService;
use Illuminate\Http\Request;

class MongoDBTestController extends Controller
{
    private $mongoService;

    public function __construct(MongoDBService $mongoService)
    {
        $this->mongoService = $mongoService;
    }

    /**
     * MongoDB bağlantı testi
     */
    public function testConnection()
    {
        try {
            $isConnected = $this->mongoService->testConnection();
            
            if ($isConnected) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'MongoDB bağlantısı başarılı!',
                    'timestamp' => now()->toISOString()
                ]);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'MongoDB bağlantısı başarısız!',
                    'timestamp' => now()->toISOString()
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Hata: ' . $e->getMessage(),
                'timestamp' => now()->toISOString()
            ], 500);
        }
    }

    /**
     * Test verisi ekle
     */
    public function insertTestData()
    {
        try {
            // Test koleksiyonu oluştur
            $this->mongoService->createCollection('test_products');
            
            // Test verisi ekle
            $testData = [
                'name' => 'Test Ürün ' . rand(1, 1000),
                'price' => rand(10, 1000),
                'category' => 'Test Kategori',
                'description' => 'Bu bir test ürünüdür',
                'stock' => rand(1, 100)
            ];
            
            $id = $this->mongoService->insert('test_products', $testData);
            
            return response()->json([
                'status' => 'success',
                'message' => 'Test verisi eklendi',
                'id' => $id,
                'data' => $testData,
                'timestamp' => now()->toISOString()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Hata: ' . $e->getMessage(),
                'timestamp' => now()->toISOString()
            ], 500);
        }
    }

    /**
     * Test verilerini listele
     */
    public function listTestData()
    {
        try {
            $data = $this->mongoService->find('test_products');
            
            return response()->json([
                'status' => 'success',
                'count' => count($data),
                'data' => $data,
                'timestamp' => now()->toISOString()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Hata: ' . $e->getMessage(),
                'timestamp' => now()->toISOString()
            ], 500);
        }
    }

    /**
     * Koleksiyonları listele
     */
    public function listCollections()
    {
        try {
            $collections = $this->mongoService->listCollections();
            
            return response()->json([
                'status' => 'success',
                'collections' => $collections,
                'count' => count($collections),
                'timestamp' => now()->toISOString()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Hata: ' . $e->getMessage(),
                'timestamp' => now()->toISOString()
            ], 500);
        }
    }

    /**
     * MongoDB dashboard
     */
    public function dashboard()
    {
        try {
            $isConnected = $this->mongoService->testConnection();
            $collections = $this->mongoService->listCollections();
            $testData = $this->mongoService->find('test_products');
            
            return view('mongodb.dashboard', compact('isConnected', 'collections', 'testData'));
        } catch (\Exception $e) {
            return view('mongodb.dashboard', [
                'isConnected' => false,
                'collections' => [],
                'testData' => [],
                'error' => $e->getMessage()
            ]);
        }
    }
}
