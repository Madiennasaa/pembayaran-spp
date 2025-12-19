<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Lupa Password - TK DHARMA</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="{{ asset('template/css') }}/sb-admin-2.min.css" rel="stylesheet">
    <style>
        * {
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: #FFB300;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-card {
            border-radius: 24px;
            box-shadow: 0 25px 70px rgba(0, 0, 0, 0.15);
            border: 1px solid rgba(255, 214, 112, 0.25);
        }

        .form-control-user {
            border-radius: 50px;
            padding: 1.5rem;
            border: 2px solid #FFE8A3;
        }

        .btn-primary {
            border-radius: 50px;
            background: linear-gradient(135deg, #FFB300, #FF8C00);
            border: none;
            font-weight: 600;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="card login-card mx-auto" style="max-width: 500px;">
            <div class="card-body p-5">
                <div class="text-center mb-4">
                    <h1 class="h4 text-gray-900 fw-bold">Lupa Password?</h1>
                    <p class="text-muted small">Kami akan mengirimkan link reset ke email Anda.</p>
                </div>

                @if (session('status'))
                    <div class="alert alert-success small" style="border-radius: 12px;">{{ session('status') }}</div>
                @endif

                <form action="{{ route('password.email') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <input type="email" name="email"
                            class="form-control form-control-user @error('email') is-invalid @enderror"
                            placeholder="Masukkan Email Terdaftar" value="{{ old('email') }}" required>
                        @error('email')
                            <div class="invalid-feedback ml-3">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary btn-block shadow-sm">Kirim Link Reset</button>
                </form>
                <div class="text-center mt-4">
                    <a class="small text-warning" href="{{ route('login') }}">Kembali ke Login</a>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
