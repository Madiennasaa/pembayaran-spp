<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Aplikasi Laravel - Login</title>

    <link href="{{ asset('template/vendor') }}/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="{{ asset('template/css') }}/sb-admin-2.min.css" rel="stylesheet">

    <style>
        * {
            font-family: 'Poppins', sans-serif;
        }

        html {
            scroll-behavior: smooth;
        }

        /* =====================
           BODY & SCROLL FIX
           ===================== */
        body {
            background: #fffae3;
            min-height: 100vh;
            position: relative;
            overflow-y: auto;
        }

        /* Center hanya desktop */
        @media (min-width: 992px) {
            body {
                display: flex;
                align-items: center;
                justify-content: center;
                overflow: hidden;
            }
        }

        body::before,
        body::after {
            content: '';
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            z-index: 0;
        }

        /* body::before {
            top: -40%;
            right: -20%;
            width: 600px;
            height: 600px;
            background: rgba(255, 230, 163, 0.3);
        }

        body::after {
            bottom: -30%;
            left: -15%;
            width: 500px;
            height: 500px;
            background: rgba(255, 214, 112, 0.3);
        } */

        .container {
            position: relative;
            z-index: 1;
            padding: 30px 15px;
        }

        /* =====================
           LOGIN CARD
           ===================== */
        .login-card {
            border-radius: 24px;
            overflow: hidden;
            background: rgba(255, 255, 255, 0.97);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 214, 112, 0.25);
            box-shadow:
                0 0 30px rgba(255, 193, 7, 0.3),
                0 25px 70px rgba(0, 0, 0, 0.25);
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

        /* =====================
           LOGO SECTION
           ===================== */
        .logo-section {
            background: linear-gradient(135deg, #FFE8A3, #FFD670);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 300px;
        }

        .logo-section img {
            max-width: 240px;
            filter: drop-shadow(0 10px 30px rgba(0, 0, 0, 0.15));
        }

        /* =====================
           FORM
           ===================== */
        .form-section {
            padding: 3rem;
        }

        .welcome-text h1 {
            color: #FF8C00;
            font-weight: 700;
            font-size: 1.7rem;
        }

        .welcome-text small {
            color: #FFA500;
            font-size: 0.8rem;
            letter-spacing: 2px;
        }

        .form-control-user {
            border-radius: 50px;
            padding: 1rem 1.5rem;
            border: 2px solid #FFE8A3;
            background: rgba(255, 248, 230, 0.6);
        }

        .btn-primary {
            border-radius: 50px;
            background: linear-gradient(135deg, #FFB300, #FF8C00);
            border: none;
            font-weight: 600;
            box-shadow: 0 8px 20px rgba(255, 140, 0, 0.35);
        }

        /* =====================
           RESPONSIVE
           ===================== */
        @media (max-width: 991px) {
            .logo-section {
                min-height: 220px;
            }

            .logo-section img {
                max-width: 200px;
            }

            .form-section {
                padding: 2rem;
            }
        }

        @media (max-width: 576px) {
            .login-card {
                border-radius: 18px;
            }

            .logo-section {
                min-height: 180px;
            }

            .logo-section img {
                max-width: 170px;
            }

            .form-section {
                padding: 1.6rem;
            }

            .welcome-text h1 {
                font-size: 1.4rem;
            }

            .welcome-text small {
                font-size: 0.7rem;
            }
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="card login-card mx-auto" style="max-width: 900px;">
            <div class="row g-0">

                <!-- LOGO -->
                <div class="col-lg-5 logo-section">
                    <img src="{{ asset('template/img') }}/Logo TK.png" alt="Logo TK">
                </div>

                <!-- FORM -->
                <div class="col-lg-7">
                    <div class="form-section">
                        <div class="text-center welcome-text mb-4">
                            <h1>Selamat Datang</h1>
                            <small>SISTEM PEMBAYARAN SPP - TK DHARMA</small>
                        </div>

                        <form action="{{ route('login') }}" method="POST">
                            @csrf

                            <div class="form-group">
                                <input type="text" name="username" class="form-control form-control-user"
                                    placeholder="Masukkan Username" required>
                            </div>

                            <div class="form-group">
                                <input type="password" name="password" class="form-control form-control-user"
                                    placeholder="Masukkan Password" required>
                            </div>

                            <div class="d-flex justify-content-between mb-3">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="remember">
                                    <label class="form-check-label" for="remember">Ingat saya</label>
                                </div>
                                {{-- <a class="small" href="{{ route('password.request') }}">Lupa Password?</a> --}}
                            </div>

                            <button type="submit" class="btn btn-primary btn-block">
                                Masuk
                            </button>
                        </form>

                    </div>
                </div>

            </div>
        </div>
    </div>

    <script src="{{ asset('template/vendor') }}/jquery/jquery.min.js"></script>
    <script src="{{ asset('template/vendor') }}/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>

</html>
