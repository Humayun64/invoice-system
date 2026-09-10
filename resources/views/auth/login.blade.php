<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login – SKM Engineering</title>
<style>
* { box-sizing:border-box; margin:0; padding:0; }
body { font-family:'Segoe UI',Arial,sans-serif; background:linear-gradient(135deg,#1e1e2e 0%,#7b1313 100%); min-height:100vh; display:flex; align-items:center; justify-content:center; }
.login-wrap { width:100%; max-width:400px; padding:20px; }
.login-card { background:#fff; border-radius:16px; padding:40px 36px; box-shadow:0 20px 60px rgba(0,0,0,0.3); }
.login-logo { text-align:center; margin-bottom:28px; }
.login-logo img { width:80px; height:80px; object-fit:cover; border-radius:16px; margin-bottom:12px; }
.login-logo h1 { font-size:1.1rem; font-weight:800; color:#1e293b; letter-spacing:0.5px; }
.login-logo p  { font-size:0.82rem; color:#64748b; margin-top:3px; }
.form-group { margin-bottom:16px; }
label { display:block; font-size:0.78rem; font-weight:600; color:#475569; text-transform:uppercase; letter-spacing:0.4px; margin-bottom:6px; }
input { width:100%; padding:11px 14px; border:1px solid #e2e8f0; border-radius:8px; font-size:0.95rem; color:#1e293b; transition:border 0.15s; }
input:focus { outline:none; border-color:#7b1313; box-shadow:0 0 0 3px rgba(123,19,19,0.1); }
.btn-login { width:100%; padding:12px; background:#7b1313; color:#fff; border:none; border-radius:8px; font-size:1rem; font-weight:700; cursor:pointer; margin-top:8px; }
.btn-login:hover { background:#9b1a1a; }
.error-box { background:#fef2f2; border:1px solid #fca5a5; color:#b91c1c; padding:10px 14px; border-radius:8px; font-size:0.88rem; margin-bottom:16px; }
</style>
</head>
<body>
<div class="login-wrap">
  <div class="login-card">
    <div class="login-logo">
      <img src="{{ asset('assets/logo.png') }}" alt="SKM" onerror="this.style.display='none'"><br>
      <h1>SKM ENGINEERING PTE LTD</h1>
      <p>Invoice & Quotation System</p>
    </div>

    @if($errors->any())
      <div class="error-box">⚠️ {{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('login.post') }}">
      @csrf
      <div class="form-group">
        <label>Username</label>
        <input type="text" name="username" value="{{ old('username') }}" placeholder="Enter username" autofocus required>
      </div>
      <div class="form-group">
        <label>Password</label>
        <input type="password" name="password" placeholder="Enter password" required>
      </div>
      <button type="submit" class="btn-login">🔐 Login</button>
    </form>
  </div>
</div>
</body>
</html>
