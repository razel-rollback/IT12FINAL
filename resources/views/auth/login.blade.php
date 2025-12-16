<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login - CRM FruitStand</title>
<script src="https://cdn.tailwindcss.com"></script>
<style>
  /* Glass Panel with soft reflections */
  .glass {
      background: rgba(255,255,255,0.08);
      backdrop-filter: blur(24px);
      -webkit-backdrop-filter: blur(24px);
      border-radius: 1.5rem;
      border: 1px solid rgba(255,255,255,0.2);
      box-shadow: 0 20px 60px rgba(0,0,0,0.15), inset 0 1px 0 rgba(255,255,255,0.2);
      position: relative;
      overflow: hidden;
  }
  .glass::before {
      content: '';
      position: absolute;
      top: -50%;
      left: -50%;
      width: 200%;
      height: 200%;
      background: radial-gradient(circle at 30% 30%, rgba(255,255,255,0.15), transparent 70%);
      pointer-events: none;
  }

  /* Form Inputs */
  .form-group { position: relative; }
  .form-input {
      width: 100%;
      padding: 1.1rem 1rem 0.5rem;
      border-radius: 0.75rem;
      border: 1px solid #d1d5db;
      background: rgba(255,255,255,0.85);
      backdrop-filter: blur(12px);
      font-size: 1rem;
      transition: all 0.3s ease;
      box-shadow: inset 0 2px 6px rgba(0,0,0,0.08);
  }
  .form-input:focus {
      outline: none;
      border-color: #10b981;
      box-shadow: 0 0 0 3px rgba(16,185,129,0.25), inset 0 2px 6px rgba(0,0,0,0.08);
  }
  .form-label {
      position: absolute;
      left: 1rem;
      top: 1rem;
      color: #6b7280;
      font-size: 1rem;
      pointer-events: none;
      transition: all 0.3s ease;
      background: rgba(255,255,255,0.85);
      padding: 0 0.25rem;
  }
  .form-input:focus + .form-label,
  .form-input:not(:placeholder-shown) + .form-label {
      top: -0.5rem;
      font-size: 0.75rem;
      color: #10b981;
  }

  /* Sticker with 3D/Lighting Effect */
  .sticker {
      width: 130px;
      height: 130px;
      background: url('https://i.pinimg.com/1200x/c3/60/70/c360704c2fc4da0a806b1972407ca80b.jpg') center/cover no-repeat;
      border-radius: 50%;
      border: 4px solid rgba(255,255,255,0.7);
      box-shadow: 0 20px 40px rgba(0,0,0,0.25), inset 0 6px 12px rgba(255,255,255,0.2);
      animation: float 4s ease-in-out infinite alternate;
      position: relative;
    }
  .sticker::after {
      content: '';
      position: absolute;
      top: 10%;
      left: 10%;
      width: 80%;
      height: 80%;
      border-radius: 50%;
      background: radial-gradient(circle at 30% 30%, rgba(255,255,255,0.4), transparent 60%);
      pointer-events: none;
    }
  @keyframes float {
      0% { transform: translateY(0) rotate(-2deg); }
      100% { transform: translateY(-10px) rotate(3deg); }
  }

  /* Login Button with 3D hover */
  .btn-login {
      background: linear-gradient(135deg, #064630ff, #02130dff);
      color: white;
      font-weight: 600;
      padding: 0.85rem;
      border-radius: 0.75rem;
      width: 100%;
      transition: all 0.3s ease;
      box-shadow: 0 10px 25px rgba(0,0,0,0.15), 0 4px 10px rgba(0,0,0,0.08) inset;
  }
  .btn-login:hover {
      background: linear-gradient(135deg, #112e25ff, #14523dff);
      box-shadow: 0 15px 35px rgba(0,0,0,0.25), 0 4px 10px rgba(255,255,255,0.15) inset;
      transform: translateY(-2px);
  }

  /* Left Panel Text Styling */
  .left-panel h1 {
      font-size: 3.2rem;
      font-weight: 800;
      color: white;
      text-shadow: 2px 6px 15px rgba(0,0,0,0.4);
  }
  .left-panel p {
      font-size: 1.125rem;
      color: #d1fae5;
      line-height: 1.7;
  }
  .left-panel::before {
      content: '';
      position: absolute;
      inset: 0;
      background: linear-gradient(135deg, rgba(0,0,0,0.25), rgba(0,0,0,0.05));
      z-index: 0;
  }

  /* Responsive Adjustments */
  @media (max-width: 768px) {
      .left-panel, .right-panel { width: 100%; }
      .sticker { width: 110px; height: 110px; }
      .glass { padding: 3rem 2rem; }
  }

</style>
</head>
<body class="bg-gradient-to-br from-green-100 via-green-200 to-green-300 h-screen flex items-center justify-center">

<div class="flex flex-col md:flex-row w-full max-w-6xl rounded-3xl overflow-hidden shadow-2xl">

  <!-- Left Panel -->
  <div class="md:w-1/2 left-panel bg-gradient-to-tr from-green-700 to-green-800 text-white flex flex-col items-center justify-center p-12 relative">
    <div class="relative z-10 flex flex-col items-center text-center space-y-6">
      <div class="sticker"></div>
      <h1>CRM FruitStand</h1>
      <p>Effortlessly manage inventory, sales, and customers with a modern, intuitive interface.</p>
    </div>
  </div>

  <!-- Right Panel -->
  <div class="md:w-1/2 right-panel flex items-center justify-center p-10 bg-white relative">
    <div class="w-full max-w-md glass p-10">
      <h2 class="text-3xl font-bold text-gray-700 mb-6 text-center">Welcome Back</h2>

      @if($errors->any())
        <div class="bg-red-100 text-red-700 p-3 rounded mb-4 text-center font-medium">
            {{ $errors->first() }}
        </div>
      @endif

      <form action="{{ route('login') }}" method="POST" class="space-y-6">
        @csrf
        <div class="form-group">
          <input type="email" name="email" placeholder=" " required class="form-input">
          <label class="form-label">Email</label>
        </div>
        <div class="form-group">
          <input type="password" name="password" placeholder=" " required class="form-input">
          <label class="form-label">Password</label>
        </div>
        <button type="submit" class="btn-login">Login</button>
      </form>

      <p class="text-center text-gray-400 mt-6 text-sm">
        &copy; {{ date('Y') }} CRM FruitStand. All rights reserved.
      </p>
    </div>
  </div>

</div>

</body>
</html>
