<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة الوكيل - إدارة الإنترنت</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Tajawal', sans-serif; }
    </style>
</head>
<body class="bg-gray-100 min-h-screen">

    <!-- Header -->
    <div class="bg-white shadow-lg p-5 flex items-center justify-between sticky top-0 z-10">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">لوحة الوكيل</h1>
            <p class="text-gray-500 text-sm mt-1" id="agentNameDisplay">جاري التحميل...</p>
        </div>
        <div class="flex items-center gap-4">
            <div class="flex items-center gap-2">
                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-user-tie text-blue-600 text-xl"></i>
                </div>
                <span class="text-gray-700 font-medium" id="agentUsername"></span>
            </div>
            <button onclick="logout()" class="text-red-500 hover:text-red-700">
                <i class="fas fa-sign-out-alt text-xl"></i>
            </button>
        </div>
    </div>

    <!-- البطاقات فقط -->
    <div class="p-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            
            <!-- بطاقة عدد المشتركين (قابلة للضغط) -->
            <a href="/agent/clients/all" 
               class="bg-white rounded-2xl shadow-lg p-6 text-center hover:shadow-xl transition-all hover:-translate-y-1 cursor-pointer block border-r-4 border-blue-500">
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-users text-blue-600 text-2xl"></i>
                </div>
                <p class="text-gray-500 text-sm">عدد المشتركين</p>
                <p class="text-3xl font-bold text-gray-800" id="totalClients">0</p>
            </a>

            <!-- بطاقة المشتركين النشطين -->
            <div class="bg-white rounded-2xl shadow-lg p-6 text-center border-r-4 border-green-500">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-check-circle text-green-600 text-2xl"></i>
                </div>
                <p class="text-gray-500 text-sm">المشتركين النشطين</p>
                <p class="text-3xl font-bold text-gray-800" id="activeClients">0</p>
            </div>

            <!-- بطاقة المتصلين حالياً -->
            <div class="bg-white rounded-2xl shadow-lg p-6 text-center border-r-4 border-purple-500">
                <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-wifi text-purple-600 text-2xl"></i>
                </div>
                <p class="text-gray-500 text-sm">المتصلين حالياً</p>
                <p class="text-3xl font-bold text-gray-800" id="onlineClients">0</p>
            </div>

            <!-- بطاقة المنتهى اشتراكهم (قابلة للضغط) -->
            <a href="/agent/clients/expired" 
               class="bg-white rounded-2xl shadow-lg p-6 text-center hover:shadow-xl transition-all hover:-translate-y-1 cursor-pointer block border-r-4 border-red-500">
                <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-calendar-times text-red-600 text-2xl"></i>
                </div>
                <p class="text-gray-500 text-sm">المنتهى اشتراكهم</p>
                <p class="text-3xl font-bold text-gray-800" id="expiredClients">0</p>
            </a>
        </div>
    </div>

    <script>
        let currentAgent = null;
        try {
            currentAgent = JSON.parse(sessionStorage.getItem('currentAgent'));
        } catch(e) {}
        
        if (!currentAgent || !currentAgent.username) {
            window.location.href = '/login';
        }
        
        document.getElementById('agentNameDisplay').innerText = currentAgent.name || currentAgent.username;
        document.getElementById('agentUsername').innerText = currentAgent.username;

        let clients = JSON.parse(localStorage.getItem(`clients_${currentAgent.username}`)) || [];

        function calculateDaysRemaining(endDate) {
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            const end = new Date(endDate);
            end.setHours(0, 0, 0, 0);
            return Math.ceil((end - today) / (1000 * 60 * 60 * 24));
        }

        function updateStats() {
            const total = clients.length;
            let active = 0, expired = 0;
            clients.forEach(client => {
                if (calculateDaysRemaining(client.end_date) >= 0) active++;
                else expired++;
            });
            document.getElementById('totalClients').innerText = total;
            document.getElementById('activeClients').innerText = active;
            document.getElementById('expiredClients').innerText = expired;
            document.getElementById('onlineClients').innerText = "0";
        }

        function logout() {
            sessionStorage.removeItem('currentAgent');
            window.location.href = '/login';
        }

        updateStats();
    </script>

</body>
</html>