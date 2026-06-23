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
        body { font-family: 'Tajawal', sans-serif; }
    </style>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="relative min-h-screen flex items-center justify-center p-4 overflow-hidden" style="background: linear-gradient(135deg, #e0e7ff 0%, #f3e8ff 100%);">

    <div class="absolute -top-40 -right-40 w-96 h-96 bg-blue-500 rounded-full opacity-20 blur-3xl"></div>
    <div class="absolute -bottom-40 -left-40 w-96 h-96 bg-indigo-500 rounded-full opacity-20 blur-3xl"></div>

    <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-md p-8 backdrop-blur-sm">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800">تسجيل دخول</h1>
            <p class="text-gray-500 mt-2">مرحباً بك مرة أخرى! يرجى تسجيل الدخول<br>لمتابعة حسابك</p>
        </div>

        <form id="loginForm" class="space-y-6">
            <div>
                <label class="block text-gray-700 font-medium mb-2">اسم المستخدم</label>
                <div class="relative">
                    <i class="fas fa-user absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 text-lg"></i>
                    <input type="text" id="username" 
                           class="w-full pr-12 pl-4 py-3 border border-gray-300 rounded-2xl focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200 text-lg"
                           placeholder="أدخل اسم المستخدم" required>
                </div>
            </div>

            <div>
                <label class="block text-gray-700 font-medium mb-2">كلمة المرور</label>
                <div class="relative">
                    <i class="fas fa-lock absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 text-lg"></i>
                    <input type="password" id="password" 
                           class="w-full pr-12 pl-4 py-3 border border-gray-300 rounded-2xl focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200 text-lg"
                           placeholder="أدخل كلمة المرور" required>
                </div>
            </div>

            <button type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-2xl text-lg transition">
                تسجيل دخول
            </button>
        </form>
    </div>

    <script>
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