<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل دخول</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    <style>
        body { 
            font-family: 'Tajawal', sans-serif; 
            background-color: #ffffff; 
            margin: 0;
            padding: 0;
        }
        .login-card {
            width: 100vw;
            height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            background: #ffffff;
        }
        .loader-ring {
            border-color: #f3f4f6; 
            border-top-color: #2563eb; 
            animation: spin 2.5s linear infinite;
        }
        @keyframes spin { 
            0% { transform: rotate(0deg); } 
            100% { transform: rotate(360deg); } 
        }
    </style>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>

    <div class="login-card p-4">
        <div class="w-full max-w-[400px]">
            <div class="flex justify-center mb-6 relative">
                <div class="relative w-[90px] h-[90px] flex items-center justify-center">
                    <div class="absolute inset-0 rounded-full border-[5px] loader-ring"></div>
                    <div class="w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center z-10">
                        <i class="fas fa-lock text-white text-lg"></i>
                    </div>
                </div>
            </div>

            <div class="text-center mb-8">
                <h1 class="text-[24px] font-bold text-gray-900 mb-2">تسجيل دخول</h1>
                <p class="text-gray-400 text-[12px] font-medium leading-relaxed">مرحباً بك مرة أخرى! يرجى تسجيل الدخول<br>لمتابعة حسابك</p>
            </div>

            <form id="loginForm" class="space-y-4">
                <div>
                    <label class="block text-gray-800 font-bold text-sm mb-2">اسم المستخدم</label>
                    <div class="relative">
                        <i class="fas fa-user absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-300 text-sm"></i>
                        <input type="text" id="username" 
                               class="w-full pr-10 pl-4 py-3 bg-gray-50 border border-gray-100 rounded-2xl focus:outline-none focus:border-blue-500 text-sm font-medium"
                               placeholder="أدخل اسم المستخدم" required>
                    </div>
                </div>

                <div>
                    <label class="block text-gray-800 font-bold text-sm mb-2">كلمة المرور</label>
                    <div class="relative">
                        <i class="fas fa-lock absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-300 text-sm"></i>
                        <input type="password" id="password" 
                               class="w-full pr-10 pl-10 py-3 bg-gray-50 border border-gray-100 rounded-2xl focus:outline-none focus:border-blue-500 text-sm font-medium"
                               placeholder="أدخل كلمة المرور" required>
                        <button type="button" onclick="togglePassword()" class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-300">
                            <i class="fas fa-eye" id="eyeIcon"></i>
                        </button>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <input type="checkbox" id="remember" class="w-4 h-4 text-blue-600 rounded cursor-pointer">
                    <label for="remember" class="text-sm text-gray-400 font-medium cursor-pointer">تذكرني</label>
                </div>

                <button type="submit"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3.5 rounded-2xl text-[14px] transition-all">
                    تسجيل دخول <i class="fas fa-sign-in-alt mr-2"></i>
                </button>
            </form>
        </div>
    </div>

    <script>
        function togglePassword() {
            const p = document.getElementById('password');
            const i = document.getElementById('eyeIcon');
            p.type = p.type === 'password' ? 'text' : 'password';
            i.className = p.type === 'password' ? 'fas fa-eye' : 'fas fa-eye-slash';
        }

        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const username = document.getElementById('username').value;
            const password = document.getElementById('password').value;

            // 1. التحقق من دخول الأدمن (ثابت)
            if (username === "admin@essa" && password === "1998") {
                window.location.href = "/admin";
                return;
            }

            // 2. التحقق من دخول الوكيل (عن طريق API)
            fetch('/api/agent-login', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ username: username, password: password })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    // حفظ بيانات الوكيل في sessionStorage (هذا هو الحل)
                    sessionStorage.setItem('currentAgent', JSON.stringify(data.agent));
                    window.location.href = "/agent/dashboard";
                } else {
                    alert("بيانات الدخول غير صحيحة");
                }
            })
            .catch(err => {
                console.error(err);
                alert("حدث خطأ في الاتصال بالخادم");
            });
        });
    </script>
</body>
</html>