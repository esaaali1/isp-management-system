<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>المشتركين - إدارة الإنترنت</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="bg-gray-100 min-h-screen">

    <!-- Header -->
    <div class="bg-white shadow p-4 flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-800">المشتركين</h1>
        <div class="text-sm text-gray-500">50 مشترك</div>
    </div>

    <!-- Clients List -->
    <div class="p-4 space-y-4 pb-24">
        
        <!-- مثال على عميل (يمكنك تكراره لاحقاً) -->
        <div class="bg-white rounded-2xl shadow p-4 flex items-center gap-4">
            <div class="w-12 h-12 bg-green-100 text-green-600 rounded-xl flex items-center justify-center text-2xl font-bold">
                12
            </div>
            <div class="flex-1">
                <div class="font-semibold text-lg">أحمد محمد</div>
                <div class="text-gray-500 text-sm">سرعة: 20 ميجا</div>
            </div>
            <div class="text-right">
                <div class="text-green-600 font-bold">150 ر.س</div>
                <div class="text-xs text-gray-400">30 يوم</div>
            </div>
        </div>

        <!-- عميل آخر -->
        <div class="bg-white rounded-2xl shadow p-4 flex items-center gap-4">
            <div class="w-12 h-12 bg-orange-100 text-orange-600 rounded-xl flex items-center justify-center text-2xl font-bold">
                3
            </div>
            <div class="flex-1">
                <div class="font-semibold text-lg">خالد علي</div>
                <div class="text-gray-500 text-sm">سرعة: 12 ميجا</div>
            </div>
            <div class="text-right">
                <div class="text-green-600 font-bold">100 ر.س</div>
                <div class="text-xs text-gray-400">قارب على الانتهاء</div>
            </div>
        </div>

    </div>

    <!-- Floating + Button -->
    <button onclick="showAddModal()" 
            class="fixed bottom-6 left-6 w-14 h-14 bg-blue-600 hover:bg-blue-700 text-white rounded-2xl shadow-lg flex items-center justify-center text-3xl transition-all">
        +
    </button>

    <!-- Add Client Modal (سيظهر عند الضغط على +) -->
    <div id="addModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
        <div class="bg-white rounded-3xl w-full max-w-md mx-4 p-6">
            <h2 class="text-xl font-bold mb-4">إضافة عميل جديد</h2>
            
            <div class="space-y-4">
                <input type="text" id="name" placeholder="اسم العميل" 
                       class="w-full p-4 border rounded-2xl text-lg">
                
                <input type="text" id="username" placeholder="اسم المستخدم (PPPoE)" 
                       class="w-full p-4 border rounded-2xl text-lg">
                
                <input type="password" id="password" placeholder="كلمة المرور" 
                       class="w-full p-4 border rounded-2xl text-lg">
                
                <input type="date" id="start_date" 
                       class="w-full p-4 border rounded-2xl text-lg">
            </div>

            <div class="flex gap-3 mt-6">
                <button onclick="hideAddModal()" 
                        class="flex-1 py-4 text-gray-600 font-semibold border rounded-2xl">
                    إلغاء
                </button>
                <button onclick="addClient()" 
                        class="flex-1 py-4 bg-blue-600 text-white font-semibold rounded-2xl">
                    إضافة العميل
                </button>
            </div>
        </div>
    </div>

    <script>
        function showAddModal() {
            document.getElementById('addModal').classList.remove('hidden');
        }
        
        function hideAddModal() {
            document.getElementById('addModal').classList.add('hidden');
        }
        
        function addClient() {
            alert('سيتم إضافة العميل قريباً (الربط مع المايكروتيك لاحقاً)');
            hideAddModal();
        }
    </script>

</body>
</html>