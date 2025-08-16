<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class MongoDBService
{
    private $baseUrl;
    private $database;
    private $apiKey;

    public function __construct()
    {
        $host = env('MONGODB_HOST', '127.0.0.1');
        $port = env('MONGODB_PORT', '27017');
        $this->database = env('MONGODB_DATABASE', 'e_ticaret_mongo');
        $this->apiKey = env('MONGODB_API_KEY', '');
        
        // MongoDB HTTP API endpoint (eğer MongoDB Atlas kullanıyorsanız)
        if (env('MONGODB_USE_ATLAS', false)) {
            $this->baseUrl = "https://data.mongodb-api.com/app/{$this->apiKey}/endpoint/data/v1";
        } else {
            // Local MongoDB için basit HTTP wrapper
            $this->baseUrl = "http://{$host}:{$port}/api";
        }
    }

    /**
     * Bağlantıyı test et
     */
    public function testConnection(): bool
    {
        try {
            // Basit ping testi
            $response = Http::timeout(5)->get("http://127.0.0.1:27017");
            return true;
        } catch (\Exception $e) {
            // MongoDB çalışıyor mu kontrol et
            return $this->isMongoDBRunning();
        }
    }

    /**
     * MongoDB'nin çalışıp çalışmadığını kontrol et
     */
    private function isMongoDBRunning(): bool
    {
        try {
            $connection = @fsockopen('127.0.0.1', 27017, $errno, $errstr, 5);
            if ($connection) {
                fclose($connection);
                return true;
            }
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Veri ekle (simüle edilmiş)
     */
    public function insert(string $collection, array $data): string
    {
        // Gerçek MongoDB bağlantısı olmadığı için dosyaya kaydet
        $filename = storage_path("app/mongodb_{$collection}.json");
        $existingData = [];
        
        if (file_exists($filename)) {
            $existingData = json_decode(file_get_contents($filename), true) ?? [];
        }
        
        $data['_id'] = uniqid();
        $data['created_at'] = now()->toISOString();
        $existingData[] = $data;
        
        file_put_contents($filename, json_encode($existingData, JSON_PRETTY_PRINT));
        
        return $data['_id'];
    }

    /**
     * Veri bul (simüle edilmiş)
     */
    public function find(string $collection, array $filter = [], array $options = []): array
    {
        $filename = storage_path("app/mongodb_{$collection}.json");
        
        if (!file_exists($filename)) {
            return [];
        }
        
        $data = json_decode(file_get_contents($filename), true) ?? [];
        
        // Basit filtreleme
        if (!empty($filter)) {
            $data = array_filter($data, function($item) use ($filter) {
                foreach ($filter as $key => $value) {
                    if (!isset($item[$key]) || $item[$key] != $value) {
                        return false;
                    }
                }
                return true;
            });
        }
        
        return array_values($data);
    }

    /**
     * Tek veri bul
     */
    public function findOne(string $collection, array $filter = []): ?array
    {
        $results = $this->find($collection, $filter);
        return $results[0] ?? null;
    }

    /**
     * Veri güncelle
     */
    public function update(string $collection, array $filter, array $update): int
    {
        $filename = storage_path("app/mongodb_{$collection}.json");
        
        if (!file_exists($filename)) {
            return 0;
        }
        
        $data = json_decode(file_get_contents($filename), true) ?? [];
        $updatedCount = 0;
        
        foreach ($data as &$item) {
            $match = true;
            foreach ($filter as $key => $value) {
                if (!isset($item[$key]) || $item[$key] != $value) {
                    $match = false;
                    break;
                }
            }
            
            if ($match) {
                $item = array_merge($item, $update);
                $item['updated_at'] = now()->toISOString();
                $updatedCount++;
            }
        }
        
        file_put_contents($filename, json_encode($data, JSON_PRETTY_PRINT));
        
        return $updatedCount;
    }

    /**
     * Veri sil
     */
    public function delete(string $collection, array $filter): int
    {
        $filename = storage_path("app/mongodb_{$collection}.json");
        
        if (!file_exists($filename)) {
            return 0;
        }
        
        $data = json_decode(file_get_contents($filename), true) ?? [];
        $originalCount = count($data);
        
        $data = array_filter($data, function($item) use ($filter) {
            foreach ($filter as $key => $value) {
                if (isset($item[$key]) && $item[$key] == $value) {
                    return false;
                }
            }
            return true;
        });
        
        file_put_contents($filename, json_encode(array_values($data), JSON_PRETTY_PRINT));
        
        return $originalCount - count($data);
    }

    /**
     * Koleksiyon oluştur
     */
    public function createCollection(string $collectionName): bool
    {
        $filename = storage_path("app/mongodb_{$collectionName}.json");
        
        if (!file_exists($filename)) {
            file_put_contents($filename, json_encode([], JSON_PRETTY_PRINT));
            return true;
        }
        
        return false;
    }

    /**
     * Koleksiyon listesi
     */
    public function listCollections(): array
    {
        $collections = [];
        $files = glob(storage_path("app/mongodb_*.json"));
        
        foreach ($files as $file) {
            $collectionName = str_replace(['mongodb_', '.json'], '', basename($file));
            $collections[] = $collectionName;
        }
        
        return $collections;
    }
}
