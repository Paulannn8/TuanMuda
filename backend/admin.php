<?php
session_start();

// Jika belum login, redirect ke login
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - TuanMuda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            background: #f5f5f5;
            font-family: 'Poppins', sans-serif;
        }

        .admin-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .stat-card {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 1.5rem;
        }

        .stat-card h3 {
            font-size: 2rem;
            font-weight: bold;
            margin: 0;
        }

        .stat-card p {
            color: #666;
            margin: 0;
        }

        .transaction-table {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .badge-pending {
            background: #ffc107;
            color: #000;
        }

        .badge-success {
            background: #28a745;
            color: #fff;
        }

        .badge-failed {
            background: #dc3545;
            color: #fff;
        }

        .filter-section {
            background: white;
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .action-buttons {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .btn-action {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }

        table {
            font-size: 0.9rem;
        }

        .text-truncate-custom {
            max-width: 150px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        @media (max-width: 768px) {
            .btn {
                margin-bottom: 0.2rem;
            }
        }
    </style>
</head>

<body>
    <div class="admin-header">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1><i class="bi bi-speedometer2"></i> Admin Dashboard TuanMuda</h1>
                    <p class="mb-0">Kelola dan monitor semua transaksi</p>
                </div>
                <div class="text-end">
                    <p class="mb-1 text-white">
                        <i class="bi bi-person-circle"></i>
                        <strong><?php echo htmlspecialchars($_SESSION['admin_username']); ?></strong>
                    </p>
                    <a href="manage_products.php" class="btn btn-light btn-sm">
                        <i class="bi bi-box-seam"></i> Produk
                    </a>
                    <a href="manage_testimonials.php" class="btn btn-light btn-sm">
                        <i class="bi bi-chat-quote"></i> Testimonial
                    </a>
                    <a href="logout.php" class="btn btn-light btn-sm">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <!-- Statistik Cards -->
        <div class="row">
            <div class="col-md-3">
                <div class="stat-card text-center">
                    <h3 id="totalTransactions">0</h3>
                    <p>Total Transaksi</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card text-center">
                    <h3 id="pendingCount">0</h3>
                    <p>Pending</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card text-center">
                    <h3 id="successCount">0</h3>
                    <p>Success</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card text-center">
                    <h3 id="totalRevenue">Rp0</h3>
                    <p>Total Pendapatan</p>
                </div>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="filter-section">
            <div class="row align-items-center">
                <div class="col-md-4">
                    <input type="text" id="searchInput" class="form-control"
                        placeholder="Cari Order ID, Produk, atau Email...">
                </div>
                <div class="col-md-3">
                    <select id="statusFilter" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="pending">Pending</option>
                        <option value="success">Success</option>
                        <option value="failed">Failed</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select id="productFilter" class="form-select">
                        <option value="">Semua Produk</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-primary w-100" onclick="loadTransactions()">
                        <i class="bi bi-arrow-clockwise"></i> Refresh
                    </button>
                </div>
            </div>
        </div>

        <!-- Tabel Transaksi -->
        <div class="transaction-table">
            <h4 class="mb-3"><i class="bi bi-receipt"></i> Daftar Transaksi</h4>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Produk</th>
                            <th>Item</th>
                            <th>User Data</th>
                            <th>Pembayaran</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="transactionTableBody">
                        <tr>
                            <td colspan="9" class="text-center">
                                <div class="spinner-border" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        let allTransactions = [];

        // Load transaksi saat halaman dibuka
        document.addEventListener('DOMContentLoaded', () => {
            loadTransactions();

            // Auto refresh setiap 30 detik
            setInterval(loadTransactions, 30000);

            // Event listener untuk filter
            document.getElementById('searchInput').addEventListener('input', filterTransactions);
            document.getElementById('statusFilter').addEventListener('change', filterTransactions);
            document.getElementById('productFilter').addEventListener('change', filterTransactions);
        });

        async function loadTransactions() {
            try {
                const response = await fetch('get_transactions.php');
                const result = await response.json();

                if (result.success) {
                    allTransactions = result.data;
                    updateStatistics(result.data);
                    populateProductFilter(result.data);
                    displayTransactions(result.data);
                } else {
                    console.error('Gagal load transaksi:', result.message);
                }
            } catch (error) {
                console.error('Error:', error);
                document.getElementById('transactionTableBody').innerHTML = `
                    <tr>
                        <td colspan="9" class="text-center text-danger">
                            <i class="bi bi-exclamation-triangle"></i> 
                            Gagal memuat data. Pastikan XAMPP running.
                        </td>
                    </tr>
                `;
            }
        }

        function updateStatistics(data) {
            const total = data.length;
            const pending = data.filter(t => t.status === 'pending').length;
            const success = data.filter(t => t.status === 'success').length;
            const revenue = data.filter(t => t.status === 'success')
                .reduce((sum, t) => sum + parseInt(t.total_price), 0);

            document.getElementById('totalTransactions').textContent = total;
            document.getElementById('pendingCount').textContent = pending;
            document.getElementById('successCount').textContent = success;
            document.getElementById('totalRevenue').textContent =
                'Rp' + revenue.toLocaleString('id-ID');
        }

        function populateProductFilter(data) {
            const products = [...new Set(data.map(t => t.product_name))];
            const select = document.getElementById('productFilter');

            select.innerHTML = '<option value="">Semua Produk</option>';

            products.forEach(product => {
                const option = document.createElement('option');
                option.value = product;
                option.textContent = product;
                select.appendChild(option);
            });
        }

        function displayTransactions(data) {
            const tbody = document.getElementById('transactionTableBody');

            if (data.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="9" class="text-center text-muted">
                            <i class="bi bi-inbox"></i> Belum ada transaksi
                        </td>
                    </tr>
                `;
                return;
            }

            tbody.innerHTML = data.map(t => `
                <tr>
                    <td><strong>${t.order_id}</strong></td>
                    <td class="text-truncate-custom">${t.product_name}</td>
                    <td class="text-truncate-custom">${t.item_name}</td>
                    <td class="text-truncate-custom">
                        ${t.user_id ? `ID: ${t.user_id}` : ''}
                        ${t.zone_id ? ` (${t.zone_id})` : ''}
                        ${t.email ? t.email : ''}
                        ${t.phone ? t.phone : ''}
                    </td>
                    <td>${t.payment_method}</td>
                    <td>Rp${parseInt(t.total_price).toLocaleString('id-ID')}</td>
                    <td>
                        <span class="badge badge-${t.status}">
                            ${t.status.toUpperCase()}
                        </span>
                    </td>
                    <td>${new Date(t.created_at).toLocaleDateString('id-ID')}</td>
                    <td>
                        <div class="action-buttons">
                            ${t.status !== 'success' ? `
                                <button class="btn btn-success btn-sm btn-action" 
                                    onclick="updateStatus('${t.order_id}', 'success')"
                                    title="Tandai Success">
                                    <i class="bi bi-check-circle"></i>
                                </button>
                            ` : ''}
                            ${t.status !== 'failed' ? `
                                <button class="btn btn-danger btn-sm btn-action" 
                                    onclick="updateStatus('${t.order_id}', 'failed')"
                                    title="Tandai Failed">
                                    <i class="bi bi-x-circle"></i>
                                </button>
                            ` : ''}
                            ${t.status !== 'pending' ? `
                                <button class="btn btn-warning btn-sm btn-action" 
                                    onclick="updateStatus('${t.order_id}', 'pending')"
                                    title="Kembalikan ke Pending">
                                    <i class="bi bi-clock"></i>
                                </button>
                            ` : ''}
                            <button class="btn btn-secondary btn-sm btn-action" 
                                onclick="deleteTransaction('${t.order_id}')"
                                title="Hapus Transaksi">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `).join('');
        }

        function filterTransactions() {
            const search = document.getElementById('searchInput').value.toLowerCase();
            const status = document.getElementById('statusFilter').value;
            const product = document.getElementById('productFilter').value;

            const filtered = allTransactions.filter(t => {
                const matchSearch = !search ||
                    t.order_id.toLowerCase().includes(search) ||
                    t.product_name.toLowerCase().includes(search) ||
                    (t.email && t.email.toLowerCase().includes(search)) ||
                    (t.phone && t.phone.includes(search));

                const matchStatus = !status || t.status === status;
                const matchProduct = !product || t.product_name === product;

                return matchSearch && matchStatus && matchProduct;
            });

            displayTransactions(filtered);
        }

        // UPDATE STATUS
        async function updateStatus(orderId, newStatus) {
            const statusText = {
                'success': 'BERHASIL',
                'failed': 'GAGAL',
                'pending': 'PENDING'
            };

            if (!confirm(`Yakin ubah status menjadi ${statusText[newStatus]}?`)) {
                return;
            }

            try {
                const response = await fetch('update_status.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        order_id: orderId,
                        status: newStatus
                    })
                });

                const result = await response.json();

                if (result.success) {
                    alert(`✅ Status berhasil diubah menjadi ${statusText[newStatus]}!`);
                    loadTransactions(); // Reload data
                } else {
                    alert(`❌ Gagal update status: ${result.message}`);
                }
            } catch (error) {
                alert(`❌ Terjadi kesalahan: ${error.message}`);
                console.error('Error:', error);
            }
        }

        // DELETE TRANSACTION
        async function deleteTransaction(orderId) {
            if (!confirm(`Yakin ingin HAPUS transaksi ${orderId}?\n\nAksi ini tidak bisa dibatalkan!`)) {
                return;
            }

            try {
                const response = await fetch('delete_transaction.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        order_id: orderId
                    })
                });

                const result = await response.json();

                if (result.success) {
                    alert(`✅ Transaksi ${orderId} berhasil dihapus!`);
                    loadTransactions(); // Reload data
                } else {
                    alert(`❌ Gagal hapus transaksi: ${result.message}`);
                }
            } catch (error) {
                alert(`❌ Terjadi kesalahan: ${error.message}`);
                console.error('Error:', error);
            }
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>