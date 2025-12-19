<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Sistem Informasi TK')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link
        href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&family=Quattrocento:wght@400;700&display=swap"
        rel="stylesheet">

    <style>
        * {
            font-family: "DM Sans", sans-serif;
        }

        body {
            background: rgb(255, 251, 235);
            min-height: 100vh;
            position: relative;
        }

        /* body::before {
            content: '';
            position: fixed;
            top: -50%;
            right: -20%;
            width: 600px;
            height: 600px;
            background: rgba(255, 230, 163, 0.3);
            border-radius: 50%;
            filter: blur(80px);
            z-index: -1;
        }

        body::after {
            content: '';
            position: fixed;
            bottom: -30%;
            left: -10%;
            width: 500px;
            height: 500px;
            background: rgba(255, 214, 112, 0.3);
            border-radius: 50%;
            filter: blur(80px);
            z-index: -1;
        } */

        .card {
            border-radius: 15px;
            border: 1px solid rgba(255, 214, 112, 0.2);
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.95) !important;
            box-shadow: 0 20px 60px rgba(255, 193, 7, 0.15);
            animation: fadeInUp 0.6s ease;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .card-header {
            background: linear-gradient(135deg, #FFE8A3 0%, #FFD670 100%) !important;
            border-bottom: 1px solid rgba(255, 214, 112, 0.3);
            border-radius: 15px 15px 0 0 !important;
            padding: 1rem;
        }

        .card-header h5 {
            color: #FF8C00;
            font-weight: 700;
            margin: 0;
        }

        .btn-primary {
            background: linear-gradient(135deg, #FFB300 0%, #FF8C00 100%) !important;
            border: none !important;
            border-radius: 50px;
            padding: 0.625rem 1.5rem;
            font-weight: 600;
            letter-spacing: 0.5px;
            box-shadow: 0 6px 16px rgba(255, 140, 0, 0.3);
            transition: all 0.3s ease;
            color: white !important;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 24px rgba(255, 140, 0, 0.4);
            background: linear-gradient(135deg, #FF8C00 0%, #FF6F00 100%) !important;
        }

        .btn-success {
            background: linear-gradient(135deg, #28a745 0%, #20803d 100%) !important;
            border: none !important;
            border-radius: 10px;
            padding: 0.625rem 1.5rem;
            font-weight: 600;
            box-shadow: 0 6px 16px rgba(40, 167, 69, 0.3);
            transition: all 0.3s ease;
            color: white !important;
        }

        .btn-success:hover {
            /* transform: translateY(-2px); */
            box-shadow: 0 10px 24px rgba(40, 167, 69, 0.4);
        }

        .btn-warning {
            background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%) !important;
            border: none !important;
            border-radius: 50px;
            padding: 0.5rem 1rem;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(255, 193, 7, 0.3);
            transition: all 0.3s ease;
            color: white !important;
        }

        .btn-warning:hover {
            /* transform: translateY(-2px); */
            box-shadow: 0 8px 20px rgba(255, 193, 7, 0.4);
        }

        .btn-danger {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%) !important;
            border: none !important;
            border-radius: 10px;
            padding: 0.5rem 1rem;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
            transition: all 0.3s ease;
            color: white !important;
        }

        .btn-danger:hover {
            /* transform: translateY(-2px); */
            box-shadow: 0 8px 20px rgba(220, 53, 69, 0.4);
        }

        .btn-secondary {
            border-radius: 50px !important;
            padding: 0.625rem 1.5rem;
            font-weight: 600;
        }

        .form-control,
        .form-select {
            border-radius: 12px;
            border: 2px solid #FFE8A3;
            background: rgba(255, 248, 230, 0.5);
            transition: all 0.3s ease;
            padding: 0.625rem 1rem;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #FFD670;
            background: #FFFBF0;
            box-shadow: 0 0 0 4px rgba(255, 214, 112, 0.1);
        }

        .input-group-text {
            background: rgba(255, 248, 230, 0.5);
            border: 2px solid #FFE8A3;
            border-radius: 12px 0 0 12px;
        }

        .input-group .form-control {
            border-left: none;
            border-radius: 0 12px 12px 0;
        }

        .table {
            background: white;
            border-radius: 8px;
            overflow: hidden;
        }

        .table-dark {
            background: linear-gradient(135deg, #FFB300 0%, #FF8C00 100%) !important;
            color: white !important;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(255, 230, 163, 0.2);
        }

        .badge {
            padding: 0.5em 1em;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .alert {
            border-radius: 16px;
            border: none;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }

        .alert-success {
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
            color: #155724;
        }

        .alert-danger {
            background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
            color: #721c24;
        }

        /* Modal Styling - SIMPLE & WORKING */
        .modal-content {
            border-radius: 24px !important;
            border: none !important;
            overflow: hidden;
        }

        .modal-dialog-scrollable .modal-body {
            overflow-y: auto;
            max-height: calc(100vh - 200px);
        }

        .modal-header {
            background: linear-gradient(135deg, #FFE8A3 0%, #FFD670 100%) !important;
            border-bottom: 1px solid rgba(255, 214, 112, 0.3) !important;
        }

        .modal-title {
            color: #FF8C00 !important;
            font-weight: 700 !important;
        }

        .modal-footer {
            border-top: 1px solid rgba(0, 0, 0, 0.1);
        }

        .pagination .page-link {
            /* border-radius: 50%; */
            margin: 0 4px;
            border: 2px solid #FFE8A3;
            color: #FF8C00;
            font-weight: 600;
        }

        .pagination .page-link:hover {
            background: linear-gradient(135deg, #FFE8A3 0%, #FFD670 100%);
            border-color: #FFD670;
        }

        .pagination .active .page-link {
            background: linear-gradient(135deg, #FFB300 0%, #FF8C00 100%);
            border-color: #FFB300;
            color: white
        }
    </style>
</head>

<body>
    @include('layouts.navbar')

    <div class="container mt-4">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @yield('content')
    </div>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Debug: Cek apakah Bootstrap loaded
        if (typeof bootstrap !== 'undefined') {
            console.log('✅ Bootstrap loaded successfully');
            console.log('Bootstrap version:', bootstrap.Modal.VERSION);
        } else {
            console.error('❌ Bootstrap not loaded!');
        }

        // Pastikan semua modal bisa dibuka
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM loaded');

            // Test modal tambah
            const modalTambah = document.getElementById('modalTambah');
            if (modalTambah) {
                console.log('✅ Modal Tambah found');
            }

            // Hitung jumlah modal edit
            const modalEdits = document.querySelectorAll('[id^="modalEdit"]');
            console.log('✅ Found', modalEdits.length, 'Edit modals');

            // Hitung jumlah modal hapus
            const modalHapus = document.querySelectorAll('[id^="modalHapus"]');
            console.log('✅ Found', modalHapus.length, 'Delete modals');
        });

        // Event listener untuk debugging
        document.addEventListener('show.bs.modal', function(e) {
            console.log('🔵 Modal opening:', e.target.id);
        });

        document.addEventListener('shown.bs.modal', function(e) {
            console.log('✅ Modal opened:', e.target.id);
        });

        document.addEventListener('hide.bs.modal', function(e) {
            console.log('🔴 Modal closing:', e.target.id);
        });
    </script>
</body>

</html>
