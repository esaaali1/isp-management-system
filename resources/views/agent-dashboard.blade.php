<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة الوكيل - إدارة العملاء</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Tajawal', sans-serif; background-color: #f0f2f5; }
        .dropdown-menu { display: none; }
        .dropdown-menu.open { display: block; }
        .card-hover { transition: all 0.2s ease-in-out; }
        .card-hover:hover { transform: translateY(-4px); box-shadow: 0 10px 20px -10px rgba(0,0,0,0.1); }
    </style>
</head>
<body class="min-h-screen">

    <!-- ===================== HEADER المعدل (أيقونة فقط) ===================== -->
    <div class="bg-[#f0f2f5] px-6 py-4 flex items-center justify-between sticky top-0 z-20">
        <!-- أيقونة ثابتة (غير قابلة للضغط) -->
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                <i class="fas fa-chart-pie text-blue-600 text-xl"></i>
            </div>
            <span class="text-sm font-medium text-gray-600">لوحة التحكم</span>
        </div>

        <!-- القائمة المنسدلة (الملف الشخصي) -->
        <div class="relative" id="profileDropdownContainer">
            <button onclick="toggleDropdown()" class="flex items-center gap-2 bg-white px-3 py-2 rounded-full shadow-sm border border-gray-200 hover:shadow-md transition-all focus:outline-none">
                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-user-circle text-blue-600 text-2xl"></i>
                </div>
                <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
            </button>

            <div id="profileDropdown" class="dropdown-menu absolute left-0 mt-2 w-56 bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden z-50">
                <div class="px-4 py-3 border-b border-gray-100">
                    <p class="text-sm font-bold text-gray-800" id="dropdownAgentName">الوكيل</p>
                    <p class="text-xs text-gray-500 mt-0.5" id="dropdownAgentUsername">@username</p>
                </div>
                <button onclick="logout()" class="w-full text-right px-4 py-3 text-sm text-red-600 hover:bg-red-50 transition-colors flex items-center gap-2">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>تسجيل خروج</span>
                </button>
            </div>
        </div>
    </div>

    <!-- ===================== بطاقات الإحصائيات ===================== -->
    <div class="p-6 pb-28">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

            <!-- 1. عدد المشتركين (جميع) -->
            <a href="/agent/clients/all/{{ session('agent_id') }}" 
               class="bg-white rounded-2xl shadow-lg p-6 text-center hover:shadow-xl transition-all hover:-translate-y-1 cursor-pointer block border-r-4 border-blue-500 card-hover">
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-users text-blue-600 text-2xl"></i>
                </div>
                <p class="text-gray-500 text-sm">عدد المشتركين</p>
                <p class="text-3xl font-bold text-gray-800" id="totalClients">0</p>
            </a>

            <!-- 2. المشتركين النشطين -->
            <a href="/agent/clients/active/{{ session('agent_id') }}" 
               class="bg-white rounded-2xl shadow-lg p-6 text-center hover:shadow-xl transition-all hover:-translate-y-1 cursor-pointer block border-r-4 border-green-500 card-hover">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-check-circle text-green-600 text-2xl"></i>
                </div>
                <p class="text-gray-500 text-sm">المشتركين النشطين</p>
                <p class="text-3xl font-bold text-gray-800" id="activeClients">0</p>
            </a>

            <!-- 3. المتصلين حالياً -->
            <a href="/agent/clients/online/{{ session('agent_id') }}" 
               class="bg-white rounded-2xl shadow-lg p-6 text-center hover:shadow-xl transition-all hover:-translate-y-1 cursor-pointer block border-r-4 border-purple-500 card-hover">
                <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-wifi text-purple-600 text-2xl"></i>
                </div>
                <p class="text-gray-500 text-sm">المتصلين حالياً</p>
                <p class="text-3xl font-bold text-gray-800" id="onlineClients">0</p>
            </a>

            <!-- 4. المنتهى اشتراكهم -->
            <a href="/agent/clients/expired/{{ session('agent_id') }}" 
               class="bg-white rounded-2xl shadow-lg p-6 text-center hover:shadow-xl transition-all hover:-translate-y-1 cursor-pointer block border-r-4 border-red-500 card-hover">
                <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-calendar-times text-red-600 text-2xl"></i>
                </div>
                <p class="text-gray-500 text-sm">المنتهى اشتراكهم</p>
                <p class="text-3xl font-bold text-gray-800" id="expiredClients">0</p>
            </a>
        </div>
    </div>

    <script>
        // دالة القائمة المنسدلة
        function toggleDropdown() {
            document.getElementById('profileDropdown').classList.toggle('open');
        }
        document.addEventListener('click', function(event) {
            const container = document.getElementById('profileDropdownContainer');
            if (!container.contains(event.target)) {
                document.getElementById('profileDropdown').classList.remove('open');
            }
        });

        // بيانات الوكيل
        let currentAgent = JSON.parse(sessionStorage.getItem('currentAgent'));
        if (!currentAgent) window.location.href = '/login';

        // عرض اسم الوكيل في القائمة المنسدلة (تم إزالة العرض من الهيدر)
        document.getElementById('dropdownAgentName').innerText = currentAgent.name || currentAgent.username;
        document.getElementById('dropdownAgentUsername').innerText = '@' + currentAgent.username;

        // جلب الإحصائيات
        function fetchStats() {
            fetch(`/api/agent-stats/${currentAgent.id}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('totalClients').innerText = data.total;
                    document.getElementById('activeClients').innerText = data.active;
                    document.getElementById('expiredClients').innerText = data.expired;
                    document.getElementById('onlineClients').innerText = data.online || 0;
                })
                .catch(error => console.error('Error fetching stats:', error));
        }
        fetchStats();
        setInterval(fetchStats, 30000);

        function logout() {
            sessionStorage.removeItem('currentAgent');
            window.location.href = '/login';
        }
    </script>
</body>
</html>