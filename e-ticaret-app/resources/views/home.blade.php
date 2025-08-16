@extends('layouts.app')

@section('title', 'Ana Sayfa - E-Ticaret')

@section('content')
<div class="container">
    <!-- Hero Section -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="bg-primary text-white p-5 rounded">
                <h1 class="display-4">E-Ticaret Sitesine Hoş Geldiniz!</h1>
                <p class="lead">Redis ile canlı stok yönetimi ve yüksek performans</p>
                <div class="d-flex gap-2">
                    <a href="{{ route('products') }}" class="btn btn-light btn-lg">Ürünleri Keşfet</a>
                    <a href="{{ route('mongodb.dashboard') }}" class="btn btn-success btn-lg">
                        <i class="fas fa-database me-2"></i>MongoDB Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Categories -->
    @if($categories->count() > 0)
    <div class="row mb-5">
        <div class="col-12">
            <h2 class="mb-4">Kategoriler</h2>
            <div class="row">
                @foreach($categories as $category)
                <div class="col-md-3 mb-3">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-tags fa-3x text-primary mb-3"></i>
                            <h5 class="card-title">{{ $category->name }}</h5>
                            <p class="card-text">{{ $category->description }}</p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- Featured Products -->
    @if($products->count() > 0)
    <div class="row">
        <div class="col-12">
            <h2 class="mb-4">Öne Çıkan Ürünler</h2>
            <div class="row">
                @foreach($products as $product)
                <div class="col-md-3 mb-4">
                    <div class="card h-100 product-card">
                        @if($product->image)
                        <img src="{{ $product->image }}" class="card-img-top" alt="{{ $product->name }}">
                        @else
                        <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                            <i class="fas fa-image fa-3x text-muted"></i>
                        </div>
                        @endif
                        
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">{{ $product->name }}</h5>
                            <p class="card-text text-muted">{{ Str::limit($product->description, 100) }}</p>
                            
                            <div class="mt-auto">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="h5 text-primary mb-0">{{ number_format($product->price, 2) }} ₺</span>
                                    <span class="badge bg-{{ $product->stock > 10 ? 'success' : ($product->stock > 0 ? 'warning' : 'danger') }}">
                                        Stok: {{ $product->stock }}
                                    </span>
                                </div>
                                
                                <div class="d-grid">
                                    <a href="{{ route('product', $product->id) }}" class="btn btn-outline-primary">
                                        <i class="fas fa-eye me-2"></i>İncele
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            
            <div class="text-center mt-4">
                <a href="{{ route('products') }}" class="btn btn-primary btn-lg">Tüm Ürünleri Gör</a>
            </div>
        </div>
    </div>
    @else
    <div class="row">
        <div class="col-12 text-center">
            <div class="alert alert-info">
                <h4>Henüz ürün bulunmuyor</h4>
                <p>Ürünler eklendikçe burada görünecek.</p>
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Redis Status Indicator -->
<div class="position-fixed bottom-0 end-0 p-3">
    <div class="toast show" role="alert">
        <div class="toast-header">
            <i class="fas fa-database text-success me-2"></i>
            <strong class="me-auto">Redis Bağlantısı</strong>
            <small>Canlı</small>
        </div>
        <div class="toast-body">
            Stok güncellemeleri anlık olarak yapılıyor
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Sepet sayısını güncelle (örnek)
document.addEventListener('DOMContentLoaded', function() {
    // Burada AJAX ile sepet sayısını alabiliriz
    updateCartCount();
});

function updateCartCount() {
    // Redis'ten sepet sayısını al
    fetch('/api/cart/count')
        .then(response => response.json())
        .then(data => {
            document.querySelector('.cart-count').textContent = data.count || 0;
        })
        .catch(error => {
            console.log('Sepet sayısı alınamadı:', error);
        });
}
</script>
@endpush
