<?php
session_start();
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
    <title>Kelola Produk & Item - TuanMuda</title>
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
        }

        .card {
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 1.5rem;
        }

        .container-content {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            padding-top: 30px;
        }

        .product-card {
            cursor: pointer;
            transition: all 0.3s;
            height: 100%;
            width: 250px;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .product-img {
            width: 100%;
            object-fit: cover;
            border-radius: 10px 10px 0 0;
        }

        .item-badge {
            font-size: 0.85rem;
            padding: 0.25rem 0.5rem;
        }

        .modal-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .back {
            margin-left: 0.25rem;
        }
    </style>
</head>

<body>
    <div class="admin-header">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1><i class="bi bi-box-seam"></i> Kelola Produk & Item</h1>
                    <p class="mb-0">Tambah, Edit, Hapus Produk dan Item</p>
                </div>
                <div>
                    <a href="admin.php" class="btn btn-light back">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <!-- Tabs -->
        <ul class="nav nav-tabs mb-4" id="categoryTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="game-tab" data-bs-toggle="tab" data-bs-target="#game" type="button">
                    <i class="bi bi-controller"></i> Game
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="entertainment-tab" data-bs-toggle="tab" data-bs-target="#entertainment"
                    type="button">
                    <i class="bi bi-film"></i> Entertainment
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="pulsa-tab" data-bs-toggle="tab" data-bs-target="#pulsa" type="button">
                    <i class="bi bi-phone"></i> Pulsa
                </button>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content" id="categoryTabContent">
            <!-- Game Tab -->
            <div class="tab-pane fade show active" id="game" role="tabpanel">
                <button class="btn btn-primary mb-3" onclick="showAddProductModal('game')">
                    <i class="bi bi-plus-circle"></i> Tambah Produk Game
                </button>
                <div id="game-products" class="row"></div>
            </div>

            <!-- Entertainment Tab -->
            <div class="tab-pane fade" id="entertainment" role="tabpanel">
                <button class="btn btn-primary mb-3" onclick="showAddProductModal('entertainment')">
                    <i class="bi bi-plus-circle"></i> Tambah Produk Entertainment
                </button>
                <div id="entertainment-products" class="row"></div>
            </div>

            <!-- Pulsa Tab -->
            <div class="tab-pane fade" id="pulsa" role="tabpanel">
                <button class="btn btn-primary mb-3" onclick="showAddProductModal('pulsa')">
                    <i class="bi bi-plus-circle"></i> Tambah Produk Pulsa
                </button>
                <div id="pulsa-products" class="row"></div>
            </div>
        </div>
    </div>

    <!-- Modal Add/Edit Product -->
    <div class="modal fade" id="productModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="productModalTitle">Tambah Produk</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="productForm">
                        <input type="hidden" id="product_id">
                        <input type="hidden" id="product_category">

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nama Produk</label>
                                <input type="text" class="form-control" id="product_name" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Gambar Produk</label>
                                <input type="file" class="form-control" id="product_img_file" accept="image/*">
                                <small class="text-muted">Atau gunakan path manual:</small>
                                <input type="text" class="form-control mt-2" id="product_img"
                                    placeholder="assets/mlbb.jpg">
                                <div id="product_img_preview" class="mt-2"></div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Tag</label>
                                <input type="text" class="form-control" id="product_tag" placeholder="TOP UP" required>
                            </div>
                            <div class="col-md-8 mb-3">
                                <label class="form-label">Title</label>
                                <input type="text" class="form-control" id="product_title"
                                    placeholder="Top Up Mobile Legends" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Cara</label>
                                <input type="text" class="form-control" id="product_cara" placeholder="Top Up" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Label Item</label>
                                <input type="text" class="form-control" id="product_item_label" placeholder="Diamonds"
                                    required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Label Package (opsional)</label>
                                <input type="text" class="form-control" id="product_package_label"
                                    placeholder="Paketan">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tipe Input</label>
                                <select class="form-select" id="product_input_type" required
                                    onchange="toggleInputPlaceholder()">
                                    <option value="game-with-zone">Game with Zone (User ID + Zone ID)</option>
                                    <option value="game">Game (User ID only)</option>
                                    <option value="email">Email</option>
                                    <option value="phone">Phone</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Label Input</label>
                                <input type="text" class="form-control" id="product_input_label"
                                    placeholder="Masukkan User ID" required>
                            </div>
                        </div>

                        <div class="row" id="placeholder-container">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Placeholder User ID</label>
                                <input type="text" class="form-control" id="product_placeholder_userid"
                                    placeholder="Contoh: 123456789">
                            </div>
                            <div class="col-md-6 mb-3" id="zoneid-container">
                                <label class="form-label">Placeholder Zone ID</label>
                                <input type="text" class="form-control" id="product_placeholder_zoneid"
                                    placeholder="Contoh: 1234">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Help Text</label>
                            <input type="text" class="form-control" id="product_input_help"
                                placeholder="Untuk menemukan User ID...">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" onclick="saveProduct()">Simpan</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Manage Items -->
    <div class="modal fade" id="itemsModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="itemsModalTitle">Kelola Items</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="current_product_id">

                    <button class="btn btn-success btn-sm mb-3" onclick="showAddItemModal('diamond')">
                        <i class="bi bi-plus"></i> Tambah Item
                    </button>
                    <button class="btn btn-info btn-sm mb-3" onclick="showAddItemModal('package')" id="btn-add-package">
                        <i class="bi bi-plus"></i> Tambah Package
                    </button>

                    <h6>Items / Diamonds</h6>
                    <div id="items-list" class="mb-4"></div>

                    <h6 id="packages-title">Packages</h6>
                    <div id="packages-list"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Add/Edit Item -->
    <div class="modal fade" id="itemModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="itemModalTitle">Tambah Item</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="itemForm">
                        <input type="hidden" id="item_id">
                        <input type="hidden" id="item_product_id">
                        <input type="hidden" id="item_type">

                        <div class="mb-3">
                            <label class="form-label">Nama Item</label>
                            <input type="text" class="form-control" id="item_name" required>
                        </div>

                        <div class="mb-3" id="item-img-container">
                            <label class="form-label">Gambar Item - Opsional</label>
                            <input type="file" class="form-control" id="item_img_file" accept="image/*">
                            <small class="text-muted">Atau gunakan path manual:</small>
                            <input type="text" class="form-control mt-2" id="item_img" placeholder="assets/dikit.png">
                            <small class="text-muted d-block">Kosongkan jika tidak ada gambar (untuk
                                entertainment/pulsa)</small>
                            <div id="item_img_preview" class="mt-2"></div>
                        </div>

                        <div class="mb-3" id="item-bonus-container">
                            <label class="form-label">Bonus - Opsional</label>
                            <input type="text" class="form-control" id="item_bonus" placeholder="5 + 0 Bonus">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Harga (tanpa Rp.)</label>
                            <input type="number" class="form-control" id="item_price" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Urutan Tampil</label>
                            <input type="number" class="form-control" id="item_display_order" value="0">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" onclick="saveItem()">Simpan</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let allProducts = [];
        let currentEditProductId = null;
        let currentEditItemId = null;

        document.addEventListener('DOMContentLoaded', () => {
            loadAllProducts();
        });

        async function loadAllProducts() {
            try {
                const response = await fetch('get_products.php');
                const result = await response.json();

                if (result.success) {
                    allProducts = result.data;
                    displayProductsByCategory();
                }
            } catch (error) {
                console.error('Error loading products:', error);
            }
        }

        function displayProductsByCategory() {
            const categories = ['game', 'entertainment', 'pulsa'];

            categories.forEach(category => {
                const container = document.getElementById(`${category}-products`);
                const products = allProducts.filter(p => p.category === category);

                if (products.length === 0) {
                    container.innerHTML = '<p class="text-muted">Belum ada produk</p>';
                    return;
                }

                container.innerHTML = products.map(p => `
                    <div class="container-content col-md-4 mb-3">
                        <div class="card product-card">
                            <img src="../${p.img}" class="product-img" alt="${p.name}">
                            <div class="card-body">
                                <h6 class="fw-bold">${p.name}</h6>
                                <span class="badge bg-primary">${p.tag}</span>
                                <div class="mt-2">
                                    <button class="btn btn-sm btn-success" onclick="showItemsModal(${p.id}, '${p.name}')">
                                        <i class="bi bi-list"></i> Items
                                    </button>
                                    <button class="btn btn-sm btn-warning" onclick="editProduct(${p.id})">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger" onclick="deleteProduct(${p.id}, '${p.name}')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                `).join('');
            });
        }

        function showAddProductModal(category) {
            currentEditProductId = null;
            document.getElementById('productModalTitle').textContent = 'Tambah Produk';
            document.getElementById('productForm').reset();
            document.getElementById('product_category').value = category;

            new bootstrap.Modal(document.getElementById('productModal')).show();
        }

        function toggleInputPlaceholder() {
            const type = document.getElementById('product_input_type').value;
            const zoneidContainer = document.getElementById('zoneid-container');

            if (type === 'game-with-zone') {
                zoneidContainer.style.display = 'block';
            } else {
                zoneidContainer.style.display = 'none';
            }
        }

        async function editProduct(id) {
            const product = allProducts.find(p => p.id == id);
            if (!product) return;

            currentEditProductId = id;
            document.getElementById('productModalTitle').textContent = 'Edit Produk';

            document.getElementById('product_id').value = product.id;
            document.getElementById('product_category').value = product.category;
            document.getElementById('product_name').value = product.name;
            document.getElementById('product_img').value = product.img;
            document.getElementById('product_tag').value = product.tag;
            document.getElementById('product_title').value = product.title;
            document.getElementById('product_cara').value = product.cara;
            document.getElementById('product_item_label').value = product.item_label;
            document.getElementById('product_package_label').value = product.package_label || '';
            document.getElementById('product_input_type').value = product.input_type;
            document.getElementById('product_input_label').value = product.input_label;
            document.getElementById('product_placeholder_userid').value = product.input_placeholder_userid || '';
            document.getElementById('product_placeholder_zoneid').value = product.input_placeholder_zoneid || '';
            document.getElementById('product_input_help').value = product.input_help || '';

            toggleInputPlaceholder();
            new bootstrap.Modal(document.getElementById('productModal')).show();
        }

        async function saveProduct() {
            const data = {
                name: document.getElementById('product_name').value,
                category: document.getElementById('product_category').value,
                img: document.getElementById('product_img').value,
                tag: document.getElementById('product_tag').value,
                title: document.getElementById('product_title').value,
                cara: document.getElementById('product_cara').value,
                item_label: document.getElementById('product_item_label').value,
                package_label: document.getElementById('product_package_label').value || null,
                input_type: document.getElementById('product_input_type').value,
                input_label: document.getElementById('product_input_label').value,
                input_placeholder_userid: document.getElementById('product_placeholder_userid').value || null,
                input_placeholder_zoneid: document.getElementById('product_placeholder_zoneid').value || null,
                input_help: document.getElementById('product_input_help').value || null
            };

            const url = currentEditProductId ? 'update_product.php' : 'create_product.php';
            if (currentEditProductId) {
                data.id = currentEditProductId;
            }

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                if (result.success) {
                    alert(result.message);
                    bootstrap.Modal.getInstance(document.getElementById('productModal')).hide();
                    loadAllProducts();
                } else {
                    alert('Error: ' + result.message);
                }
            } catch (error) {
                alert('Error: ' + error.message);
            }
        }

        async function deleteProduct(id, name) {
            if (!confirm(`Yakin hapus produk "${name}"?\n\nSemua items akan ikut terhapus!`)) return;

            try {
                const response = await fetch('delete_product.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id })
                });

                const result = await response.json();

                if (result.success) {
                    alert(result.message);
                    loadAllProducts();
                } else {
                    alert('Error: ' + result.message);
                }
            } catch (error) {
                alert('Error: ' + error.message);
            }
        }

        async function showItemsModal(productId, productName) {
            document.getElementById('current_product_id').value = productId;
            document.getElementById('itemsModalTitle').textContent = `Kelola Items - ${productName}`;

            // Check if product has package_label
            const product = allProducts.find(p => p.id == productId);
            if (product && product.package_label) {
                document.getElementById('btn-add-package').style.display = 'inline-block';
                document.getElementById('packages-title').style.display = 'block';
            } else {
                document.getElementById('btn-add-package').style.display = 'none';
                document.getElementById('packages-title').style.display = 'none';
            }

            await loadItems(productId);
            new bootstrap.Modal(document.getElementById('itemsModal')).show();
        }

        async function loadItems(productId) {
            try {
                const response = await fetch(`get_product_detail.php?id=${productId}`);
                const result = await response.json();

                if (result.success) {
                    displayItems(result.data.diamonds, 'items-list');
                    displayItems(result.data.packages, 'packages-list');
                }
            } catch (error) {
                console.error('Error loading items:', error);
            }
        }

        function displayItems(items, containerId) {
            const container = document.getElementById(containerId);

            if (items.length === 0) {
                container.innerHTML = '<p class="text-muted">Belum ada item</p>';
                return;
            }

            container.innerHTML = `
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Gambar</th>
                            <th>Bonus</th>
                            <th>Harga</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${items.map(item => `
                            <tr>
                                <td>${item.name}</td>
                                <td>${item.img || '-'}</td>
                                <td>${item.bonus || '-'}</td>
                                <td>Rp${parseInt(item.price).toLocaleString('id-ID')}</td>
                                <td>
                                    <button class="btn btn-sm btn-warning" onclick="editItem(${item.id}, '${item.type}')">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger" onclick="deleteItem(${item.id}, '${item.name}')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            `;
        }

        function showAddItemModal(type) {
            currentEditItemId = null;
            document.getElementById('itemModalTitle').textContent = type === 'diamond' ? 'Tambah Item' : 'Tambah Package';
            document.getElementById('itemForm').reset();
            document.getElementById('item_type').value = type;
            document.getElementById('item_product_id').value = document.getElementById('current_product_id').value;

            new bootstrap.Modal(document.getElementById('itemModal')).show();
        }

        async function editItem(id, type) {
            try {
                const productId = document.getElementById('current_product_id').value;
                const response = await fetch(`get_product_detail.php?id=${productId}`);
                const result = await response.json();

                if (result.success) {
                    const allItems = [...result.data.diamonds, ...result.data.packages];
                    const item = allItems.find(i => i.id == id);

                    if (item) {
                        currentEditItemId = id;
                        document.getElementById('itemModalTitle').textContent = 'Edit Item';
                        document.getElementById('item_id').value = item.id;
                        document.getElementById('item_product_id').value = item.product_id;
                        document.getElementById('item_type').value = item.type;
                        document.getElementById('item_name').value = item.name;
                        document.getElementById('item_img').value = item.img || '';
                        document.getElementById('item_bonus').value = item.bonus || '';
                        document.getElementById('item_price').value = item.price;
                        document.getElementById('item_display_order').value = item.display_order;

                        new bootstrap.Modal(document.getElementById('itemModal')).show();
                    }
                }
            } catch (error) {
                console.error('Error loading item:', error);
            }
        }

        async function saveItem() {
            const data = {
                product_id: parseInt(document.getElementById('item_product_id').value),
                type: document.getElementById('item_type').value,
                name: document.getElementById('item_name').value,
                img: document.getElementById('item_img').value || null,
                bonus: document.getElementById('item_bonus').value || null,
                price: parseInt(document.getElementById('item_price').value),
                display_order: parseInt(document.getElementById('item_display_order').value)
            };

            const url = currentEditItemId ? 'update_item.php' : 'create_item.php';
            if (currentEditItemId) {
                data.id = currentEditItemId;
            }

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                if (result.success) {
                    alert(result.message);
                    bootstrap.Modal.getInstance(document.getElementById('itemModal')).hide();
                    loadItems(data.product_id);
                } else {
                    alert('Error: ' + result.message);
                }
            } catch (error) {
                alert('Error: ' + error.message);
            }
        }

        async function deleteItem(id, name) {
            if (!confirm(`Yakin hapus item "${name}"?`)) return;

            try {
                const response = await fetch('delete_item.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id })
                });

                const result = await response.json();

                if (result.success) {
                    alert(result.message);
                    const productId = document.getElementById('current_product_id').value;
                    loadItems(productId);
                } else {
                    alert('Error: ' + result.message);
                }
            } catch (error) {
                alert('Error: ' + error.message);
            }
        }

        // Upload gambar produk
        document.getElementById('product_img_file').addEventListener('change', async function (e) {
            const file = e.target.files[0];
            if (!file) return;

            const formData = new FormData();
            formData.append('image', file);
            formData.append('type', 'products');

            try {
                const response = await fetch('upload_image.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    document.getElementById('product_img').value = result.path;
                    document.getElementById('product_img_preview').innerHTML =
                        `<img src="../${result.path}" style="max-width: 100px; border-radius: 5px;">`;
                    alert('✅ Gambar berhasil diupload!');
                } else {
                    alert('❌ ' + result.message);
                }
            } catch (error) {
                alert('❌ Error: ' + error.message);
            }
        });

        // Upload gambar item
        document.getElementById('item_img_file').addEventListener('change', async function (e) {
            const file = e.target.files[0];
            if (!file) return;

            const formData = new FormData();
            formData.append('image', file);
            formData.append('type', 'items');

            try {
                const response = await fetch('upload_image.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    document.getElementById('item_img').value = result.path;
                    document.getElementById('item_img_preview').innerHTML =
                        `<img src="../${result.path}" style="max-width: 100px; border-radius: 5px;">`;
                    alert('✅ Gambar berhasil diupload!');
                } else {
                    alert('❌ ' + result.message);
                }
            } catch (error) {
                alert('❌ Error: ' + error.message);
            }
        });
    </script>
</body>

</html>