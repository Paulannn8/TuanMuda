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
    <title>Kelola Testimonial - TuanMuda</title>
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

        .testimonial-card {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .testimonial-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            object-fit: cover;
        }

        .stars i {
            color: #ffc107;
        }

        .modal-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .star-rating-input {
            display: flex;
            gap: 0.5rem;
            font-size: 2rem;
        }

        .star-rating-input i {
            color: #ddd;
            cursor: pointer;
            transition: color 0.2s;
        }

        .star-rating-input i.active {
            color: #ffc107;
        }

        .star-rating-input i:hover,
        .star-rating-input i:hover~i {
            color: #ffc107;
        }

        .badge-active {
            background: #28a745;
        }

        .badge-inactive {
            background: #dc3545;
        }
    </style>
</head>

<body>
    <div class="admin-header">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1><i class="bi bi-chat-quote"></i> Kelola Testimonial</h1>
                    <p class="mb-0">Tambah, Edit, Hapus Testimonial Pelanggan</p>
                </div>
                <div>
                    <a href="admin.php" class="btn btn-light">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <button class="btn btn-primary mb-3" onclick="showAddTestimonialModal()">
            <i class="bi bi-plus-circle"></i> Tambah Testimonial
        </button>

        <div id="testimonial-list" class="row"></div>
    </div>

    <!-- Modal Add/Edit Testimonial -->
    <div class="modal fade" id="testimonialModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="testimonialModalTitle">Tambah Testimonial</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="testimonialForm">
                        <input type="hidden" id="testimonial_id">

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nama Pelanggan</label>
                                <input type="text" class="form-control" id="testimonial_name" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Gender</label>
                                <select class="form-select" id="testimonial_gender" required>
                                    <option value="">Pilih Gender</option>
                                    <option value="male">Pria</option>
                                    <option value="female">Wanita</option>
                                </select>
                                <small class="text-muted">Avatar akan dipilih otomatis sesuai gender</small>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Rating (0.5 - 5.0)</label>
                            <div class="star-rating-input" id="star-rating">
                                <i class="bi bi-star-fill" data-rating="1"></i>
                                <i class="bi bi-star-fill" data-rating="2"></i>
                                <i class="bi bi-star-fill" data-rating="3"></i>
                                <i class="bi bi-star-fill" data-rating="4"></i>
                                <i class="bi bi-star-fill" data-rating="5"></i>
                            </div>
                            <input type="hidden" id="testimonial_rating" value="5">
                            <small class="text-muted d-block mt-2">Klik untuk setengah bintang, klik 2x untuk bintang
                                penuh</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Testimonial</label>
                            <textarea class="form-control" id="testimonial_text" rows="4" required
                                placeholder="Masukkan testimonial pelanggan..."></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Urutan Tampil</label>
                            <input type="number" class="form-control" id="testimonial_order" value="0">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" onclick="saveTestimonial()">Simpan</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let allTestimonials = [];
        let currentEditId = null;
        let currentRating = 5;

        document.addEventListener('DOMContentLoaded', () => {
            loadTestimonials();
            setupStarRating();
        });

        function setupStarRating() {
            const stars = document.querySelectorAll('#star-rating i');
            let clickCount = {};

            stars.forEach(star => {
                const rating = parseFloat(star.dataset.rating);
                clickCount[rating] = 0;

                star.addEventListener('click', function () {
                    clickCount[rating]++;

                    if (clickCount[rating] === 1) {
                        // Klik pertama: setengah bintang
                        currentRating = rating - 0.5;
                        setTimeout(() => { clickCount[rating] = 0; }, 500);
                    } else if (clickCount[rating] === 2) {
                        // Klik kedua: bintang penuh
                        currentRating = rating;
                        clickCount[rating] = 0;
                    }

                    updateStarDisplay();
                    document.getElementById('testimonial_rating').value = currentRating;
                });
            });
        }

        function updateStarDisplay() {
            const stars = document.querySelectorAll('#star-rating i');
            stars.forEach(star => {
                const rating = parseFloat(star.dataset.rating);

                if (rating <= currentRating) {
                    star.classList.add('active');
                    star.classList.remove('bi-star-half');
                    star.classList.add('bi-star-fill');
                } else if (rating - 0.5 === currentRating) {
                    star.classList.add('active');
                    star.classList.remove('bi-star-fill');
                    star.classList.add('bi-star-half');
                } else {
                    star.classList.remove('active');
                    star.classList.remove('bi-star-half');
                    star.classList.add('bi-star-fill');
                }
            });
        }

        // FUNGSI INI YANG BENAR - HANYA ADA SATU
        async function loadTestimonials() {
            try {
                // TAMBAHKAN parameter show_all untuk admin
                const response = await fetch('get_testimonials.php?show_all=true');
                const result = await response.json();

                console.log('Load testimonials result:', result); // DEBUG

                if (result.success) {
                    allTestimonials = result.data;
                    console.log('Total testimonials loaded:', allTestimonials.length); // DEBUG
                    displayTestimonials();
                } else {
                    console.error('Failed to load:', result.message);
                }
            } catch (error) {
                console.error('Error loading testimonials:', error);
            }
        }

        function displayTestimonials() {
            const container = document.getElementById('testimonial-list');

            if (allTestimonials.length === 0) {
                container.innerHTML = '<p class="text-muted">Belum ada testimonial</p>';
                return;
            }

            container.innerHTML = allTestimonials.map(t => `
            <div class="col-md-6 mb-3">
                <div class="testimonial-card">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="d-flex align-items-center mb-2">
                            <img src="https://randomuser.me/api/portraits/${t.gender === 'male' ? 'men' : 'women'}/${t.avatar_seed}.jpg" 
                                 class="testimonial-avatar me-3" alt="${t.name}">
                            <div>
                                <h6 class="mb-0 fw-bold">${t.name}</h6>
                                <div class="stars">
                                    ${generateStars(t.rating)}
                                </div>
                            </div>
                        </div>
                        <span class="badge ${t.is_active == 1 ? 'badge-active' : 'badge-inactive'}">
                            ${t.is_active == 1 ? 'Aktif' : 'Nonaktif'}
                        </span>
                    </div>
                    <p class="mb-3">"${t.text}"</p>
                    <div class="d-flex gap-2">
                        <button class="btn btn-sm btn-warning" onclick="editTestimonial(${t.id})">
                            <i class="bi bi-pencil"></i> Edit
                        </button>
                        <button class="btn btn-sm ${t.is_active == 1 ? 'btn-secondary' : 'btn-success'}" 
                            onclick="toggleTestimonial(${t.id}, ${t.is_active == 1 ? 0 : 1})">
                            <i class="bi bi-${t.is_active == 1 ? 'eye-slash' : 'eye'}"></i> 
                            ${t.is_active == 1 ? 'Nonaktifkan' : 'Aktifkan'}
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="deleteTestimonial(${t.id}, '${t.name}')">
                            <i class="bi bi-trash"></i> Hapus
                        </button>
                    </div>
                </div>
            </div>
        `).join('');
        }

        function generateStars(rating) {
            let stars = '';
            const fullStars = Math.floor(rating);
            const hasHalfStar = rating % 1 !== 0;

            for (let i = 0; i < fullStars; i++) {
                stars += '<i class="bi bi-star-fill"></i>';
            }
            if (hasHalfStar) {
                stars += '<i class="bi bi-star-half"></i>';
            }
            for (let i = Math.ceil(rating); i < 5; i++) {
                stars += '<i class="bi bi-star"></i>';
            }

            return stars;
        }

        function showAddTestimonialModal() {
            currentEditId = null;
            currentRating = 5;
            document.getElementById('testimonialModalTitle').textContent = 'Tambah Testimonial';
            document.getElementById('testimonialForm').reset();
            updateStarDisplay();
            new bootstrap.Modal(document.getElementById('testimonialModal')).show();
        }

        async function editTestimonial(id) {
            const testi = allTestimonials.find(t => t.id == id);
            if (!testi) return;

            currentEditId = id;
            currentRating = parseFloat(testi.rating);

            document.getElementById('testimonialModalTitle').textContent = 'Edit Testimonial';
            document.getElementById('testimonial_id').value = testi.id;
            document.getElementById('testimonial_name').value = testi.name;
            document.getElementById('testimonial_gender').value = testi.gender;
            document.getElementById('testimonial_rating').value = testi.rating;
            document.getElementById('testimonial_text').value = testi.text;
            document.getElementById('testimonial_order').value = testi.display_order;

            updateStarDisplay();
            new bootstrap.Modal(document.getElementById('testimonialModal')).show();
        }

        async function saveTestimonial() {
            const data = {
                name: document.getElementById('testimonial_name').value,
                gender: document.getElementById('testimonial_gender').value,
                rating: parseFloat(document.getElementById('testimonial_rating').value),
                text: document.getElementById('testimonial_text').value,
                display_order: parseInt(document.getElementById('testimonial_order').value)
            };

            if (!data.name || !data.gender || !data.text) {
                alert('Mohon lengkapi semua field!');
                return;
            }

            const url = currentEditId ? 'update_testimonial.php' : 'create_testimonial.php';
            if (currentEditId) {
                data.id = currentEditId;
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
                    bootstrap.Modal.getInstance(document.getElementById('testimonialModal')).hide();
                    loadTestimonials();
                } else {
                    alert('Error: ' + result.message);
                }
            } catch (error) {
                alert('Error: ' + error.message);
            }
        }

        async function toggleTestimonial(id, isActive) {
            console.log('Toggle called:', { id, isActive }); // DEBUG

            try {
                const response = await fetch('toggle_testimonial.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id: id, is_active: isActive })
                });

                const result = await response.json();
                console.log('Toggle result:', result); // DEBUG

                if (result.success) {
                    alert('Status berhasil diubah!');
                    loadTestimonials();
                } else {
                    alert('Error: ' + result.message);
                }
            } catch (error) {
                console.error('Toggle error:', error);
                alert('Error: ' + error.message);
            }
        }

        async function deleteTestimonial(id, name) {
            if (!confirm(`Yakin hapus testimonial dari "${name}"?`)) return;

            try {
                const response = await fetch('delete_testimonial.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id })
                });

                const result = await response.json();

                if (result.success) {
                    alert(result.message);
                    loadTestimonials();
                } else {
                    alert('Error: ' + result.message);
                }
            } catch (error) {
                alert('Error: ' + error.message);
            }
        }
    </script>
</body>

</html>