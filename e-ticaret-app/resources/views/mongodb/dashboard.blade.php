<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MongoDB Dashboard - E-Ticaret</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .status-indicator {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 10px;
        }
        .status-connected { background-color: #28a745; }
        .status-disconnected { background-color: #dc3545; }
        .card-header { background-color: #f8f9fa; }
        .btn-mongo { background-color: #00ed64; border-color: #00ed64; color: white; }
        .btn-mongo:hover { background-color: #00c853; border-color: #00c853; color: white; }
    </style>
</head>
<body>
    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <h1 class="mb-4">
                    <i class="fas fa-database text-success"></i>
                    MongoDB Dashboard
                </h1>
            </div>
        </div>

        <!-- Bağlantı Durumu -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-plug"></i>
                            Bağlantı Durumu
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <span class="status-indicator {{ $isConnected ? 'status-connected' : 'status-disconnected' }}"></span>
                            <span class="h5 mb-0">
                                {{ $isConnected ? 'Bağlı' : 'Bağlantı Yok' }}
                            </span>
                        </div>
                        <p class="text-muted mt-2">
                            MongoDB Server: 127.0.0.1:27017
                        </p>
                        @if(isset($error))
                            <div class="alert alert-danger mt-2">
                                <strong>Hata:</strong> {{ $error }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-chart-bar"></i>
                            İstatistikler
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-6">
                                <h3 class="text-primary">{{ count($collections) }}</h3>
                                <p class="text-muted">Koleksiyon</p>
                            </div>
                            <div class="col-6">
                                <h3 class="text-success">{{ count($testData) }}</h3>
                                <p class="text-muted">Test Verisi</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Test İşlemleri -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-tools"></i>
                            Test İşlemleri
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <button class="btn btn-mongo w-100 mb-2" onclick="testConnection()">
                                    <i class="fas fa-plug"></i>
                                    Bağlantı Testi
                                </button>
                            </div>
                            <div class="col-md-4">
                                <button class="btn btn-primary w-100 mb-2" onclick="insertTestData()">
                                    <i class="fas fa-plus"></i>
                                    Test Verisi Ekle
                                </button>
                            </div>
                            <div class="col-md-4">
                                <button class="btn btn-info w-100 mb-2" onclick="listTestData()">
                                    <i class="fas fa-list"></i>
                                    Verileri Listele
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Koleksiyonlar -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-folder"></i>
                            Koleksiyonlar
                        </h5>
                    </div>
                    <div class="card-body">
                        @if(count($collections) > 0)
                            <ul class="list-group list-group-flush">
                                @foreach($collections as $collection)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>
                                            <i class="fas fa-database text-primary"></i>
                                            {{ $collection }}
                                        </span>
                                        <span class="badge bg-primary rounded-pill">
                                            <i class="fas fa-table"></i>
                                        </span>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-muted text-center">Henüz koleksiyon oluşturulmamış</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Test Verileri -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-list"></i>
                            Test Verileri
                        </h5>
                    </div>
                    <div class="card-body">
                        @if(count($testData) > 0)
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Ad</th>
                                            <th>Fiyat</th>
                                            <th>Stok</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach(array_slice($testData, 0, 5) as $item)
                                            <tr>
                                                <td><small>{{ substr($item['_id'], 0, 8) }}...</small></td>
                                                <td>{{ $item['name'] }}</td>
                                                <td>₺{{ $item['price'] }}</td>
                                                <td>{{ $item['stock'] }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @if(count($testData) > 5)
                                <p class="text-muted text-center">
                                    <small>Toplam {{ count($testData) }} veri, ilk 5'i gösteriliyor</small>
                                </p>
                            @endif
                        @else
                            <p class="text-muted text-center">Henüz test verisi eklenmemiş</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Sonuçlar -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-terminal"></i>
                            API Sonuçları
                        </h5>
                    </div>
                    <div class="card-body">
                        <div id="apiResults">
                            <p class="text-muted text-center">Test işlemleri burada görüntülenecek</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function showResult(message, isSuccess = true) {
            const resultsDiv = document.getElementById('apiResults');
            const alertClass = isSuccess ? 'alert-success' : 'alert-danger';
            const icon = isSuccess ? 'check-circle' : 'exclamation-triangle';
            
            resultsDiv.innerHTML = `
                <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                    <i class="fas fa-${icon}"></i>
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
        }

        function testConnection() {
            fetch('/mongodb/test-connection')
                .then(response => response.json())
                .then(data => {
                    showResult(data.message, data.status === 'success');
                })
                .catch(error => {
                    showResult('Bağlantı hatası: ' + error.message, false);
                });
        }

        function insertTestData() {
            fetch('/mongodb/insert-test-data')
                .then(response => response.json())
                .then(data => {
                    showResult(data.message, data.status === 'success');
                    setTimeout(() => location.reload(), 1000);
                })
                .catch(error => {
                    showResult('Veri ekleme hatası: ' + error.message, false);
                });
        }

        function listTestData() {
            fetch('/mongodb/list-test-data')
                .then(response => response.json())
                .then(data => {
                    showResult(`Toplam ${data.count} veri bulundu`, data.status === 'success');
                })
                .catch(error => {
                    showResult('Veri listeleme hatası: ' + error.message, false);
                });
        }
    </script>
</body>
</html>
