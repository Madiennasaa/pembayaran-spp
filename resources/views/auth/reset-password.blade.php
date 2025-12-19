<form action="{{ route('password.update') }}" method="POST">
    @csrf
    <input type="hidden" name="token" value="{{ $request->route('token') }}">
    <input type="hidden" name="email" value="{{ $request->email }}">

    <div class="form-group">
        <input type="password" name="password" class="form-control form-control-user" placeholder="Password Baru" required>
    </div>
    <div class="form-group">
        <input type="password" name="password_confirmation" class="form-control form-control-user" placeholder="Konfirmasi Password Baru" required>
    </div>
    <button type="submit" class="btn btn-primary btn-block">Reset Password</button>
</form>
