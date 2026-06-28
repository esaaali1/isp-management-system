<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل دخول - إدارة الإنترنت</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    <style>
        body { 
            font-family: 'Tajawal', sans-serif; 
            background-color: #f4f7fc;
            background-image: radial-gradient(circle at 10% 20%, rgb(226, 240, 254) 0%, rgb(255, 255, 255) 90%);
        }
        .glass-card {
            background: rgba(255, 255, 255, 1);
            box-shadow: 0 20px 50px rgba(37, 99, 235, 0.08), 0 1px 3px rgba(0,0,0,0.05);
        }
        .loader-ring {
            border-color: #eff6ff; /* blue-50 */
            border-top-color: #2563eb; /* blue-600 */
            animation: spin 2.5s linear infinite;
        }
        @keyframes spin { 
            0% { transform: rotate(0deg); } 
            100% { transform: rotate(360deg); } 
        }
    </style>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="relative min-h-screen flex items-center justify-center p-4 overflow-hidden">

    <div class="absolute top-[-10%] right-[-10%] w-[600px] h-[600px] bg-blue-100 rounded-full opacity-40 blur-3xl -z-10"></div>
    <div class="absolute bottom-[-10%] left-[-10%] w-[600px] h-[600px] bg-indigo-100 rounded-full opacity-40 blur-3xl -z-10"></div>

    <div class="glass-card rounded-[30px] w-full max-w-[420px] p-8 md:p-10 relative z-10 border border-white">
        
        <div class="flex justify-center mb-6 relative">
            <div class="relative w-[100px] h-[100px] flex items-center justify-center">
                <div class="absolute inset-0 rounded-full border-[6px] loader-ring"></div>
                <div class="w-14 h-14 bg-blue-600 rounded-full flex items-center justify-center shadow-lg shadow-blue-200 z-10">
                    <i class="fas fa-lock text-white text-xl"></i>
                </div>
                <div class="absolute top-1 right-2 w-1.5 h-1.5 bg-blue-500 rounded-full"></div>
                <div class="absolute bottom-1 left-2 w-2 h-2 bg-blue-400 rounded-full"></div>
                <div class="absolute top-10 -left-3 w-1.5 h-1.5 bg-blue-300 rounded-full"></div>
                <div class="absolute top-10 -right-3 w-1 h-1 bg-blue-400 rounded-full"></div>
            </div>
        </div>

        <div class="text-center mb-8">
            <h1 class="text-[28px] font-bold text-gray-900 mb-2.5">تسجيل دخول</h1>
            <p class="text-gray-500 text-[13px] font-medium leading-relaxed">مرحباً بك مرة أخرى! يرجى تسجيل الدخول<br>لمتابعة حسابك</p>
        </div>

        <form id="loginForm" class="space-y-5">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <i class="fas fa-user text-blue-600 text-sm"></i>
                    <label class="block text-gray-800 font-bold text-sm">اسم المستخدم</label>
                </div>
                <div class="relative">
                    <i class="fas fa-user absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm"></i>
                    <input type="text" id="username" 
                           class="w-full pr-11 pl-4 py-3.5 bg-white border border-gray-200 rounded-2xl focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-50 text-sm font-medium transition-all text-gray-700 placeholder-gray-400"
                           placeholder="أدخل اسم المستخدم" required>
                </div>
            </div>

            <div>
                <div class="flex items-center gap-2 mb-2">
                    <i class="fas fa-lock text-blue-600 text-sm"></i>
                    <label class="block text-gray-800 font-bold text-sm">كلمة المرور</label>
                </div>
                <div class="relative">
                    <i class="fas fa-lock absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm"></i>
                    <input type="password" id="password" 
                           class="w-full pr-11 pl-12 py-3.5 bg-white border border-gray-200 rounded-2xl focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-50 text-sm font-medium transition-all text-gray-700 placeholder-gray-400"
                           placeholder="أدخل كلمة المرور" required>
                    
                    <button type="button" onclick="togglePassword()" class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 focus:outline-none transition-colors">
                        <i class="fas fa-eye" id="eyeIcon"></i>
                    </button>
                </div>
            </div>

            <div class="flex items-center justify-between pt-1 pb-1">
                <div class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" id="remember" class="w-4 h-4 text-blue-600 bg-white border-gray-300 rounded focus:ring-blue-500 cursor-pointer">
                    <label for="remember" class="text-sm text-gray-500 font-medium cursor-pointer">تذكرني</label>
                </div>
            </div>

            <button type="submit"
                    class="w-full bg-[#1d4ed8] hover:bg-blue-800 active:transform active:scale-[0.98] text-white font-bold py-4 rounded-2xl text-[15px] transition-all shadow-lg shadow-blue-200 flex justify-center items-center gap-2">
                <span>تسجيل دخول</span>
                <i class="fas fa-sign-in-alt rtl:rotate-180 text-sm"></i>
            </button>
        </form>
    </div>

    <script>
        // دالة لإظهار وإخفاء كلمة المرور عند الضغط على أيقونة العين
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            }
        }

        // --- كود الـ JavaScript الخاص بك دون أي تعديل ---
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
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // حفظ بيانات الوكيل في sessionStorage
                    sessionStorage.setItem('currentAgent', JSON.stringify(data.agent));
                    window.location.href = "/agent/dashboard";
                } else {
                    alert("اسم المستخدم أو كلمة المرور غير صحيحة");
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert("حدث خطأ في الاتصال بالخادم");
            });
        });
    </script>
</body>
</html>