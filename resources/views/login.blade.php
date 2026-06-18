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
        // ========== التأكد من وجود وكلاء تجريبيين ==========
        let agents = JSON.parse(localStorage.getItem('agents')) || [];
        
        // إذا لم يوجد وكلاء، نضيف وكلاء تجريبيين
        if (agents.length === 0) {
            agents = [
                { name: "وكيل تجريبي", username: "essa1", password: "1998", start_date: "2025-01-01", end_date: "2025-12-31" },
                { name: "وكيل تجريبي 2", username: "essa2", password: "1998", start_date: "2025-01-01", end_date: "2025-12-31" },
                { name: "وكيل تجريبي 3", username: "essa7", password: "1998", start_date: "2025-01-01", end_date: "2025-12-31" }
            ];
            localStorage.setItem('agents', JSON.stringify(agents));
            console.log("تم إضافة وكلاء تجريبيين إلى localStorage");
        }
        
        // ========== معالج تسجيل الدخول ==========
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const username = document.getElementById('username').value;
            const password = document.getElementById('password').value;

            console.log("محاولة دخول باسم:", username);
            console.log("الوكلاء الموجودين:", agents);

            // 1. التحقق من دخول الأدمن
            if (username === "admin@essa" && password === "1998") {
                console.log("تم التوجيه إلى صفحة الأدمن");
                window.location.href = "/admin";
                return;
            }
            
            // 2. التحقق من دخول الوكيل
            const agent = agents.find(a => a.username === username && a.password === password);
            console.log("الوكيل الذي تم العثور عليه:", agent);
            
            if (agent) {
                // حفظ بيانات الوكيل الحالي
                sessionStorage.setItem('currentAgent', JSON.stringify(agent));
                
                // التأكد من وجود قائمة عملاء لهذا الوكيل
                const clientsKey = `clients_${agent.username}`;
                if (!localStorage.getItem(clientsKey)) {
                    localStorage.setItem(clientsKey, JSON.stringify([]));
                }
                
                console.log("تم التوجيه إلى صفحة الوكيل: /agent/dashboard");
                window.location.href = "/agent/dashboard";
            } else {
                console.log("لم يتم العثور على الوكيل");
                alert("اسم المستخدم أو كلمة المرور غير صحيحة");
            }
        });
    </script>

</body>
</html>