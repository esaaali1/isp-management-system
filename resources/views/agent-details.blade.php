<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تفاصيل الوكيل</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Tajawal', sans-serif; }
    </style>
</head>
<body class="bg-gray-100 min-h-screen">

    <div class="container mx-auto p-4 max-w-2xl">
        <div class="bg-white shadow-lg rounded-2xl p-5 mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">{{ $agent->name }}</h1>
                    <p class="text-gray-500 text-sm mt-1">تفاصيل الوكيل وحالة الاشتراك</p>
                </div>
                <a href="/admin" class="text-blue-600 hover:text-blue-700">
                    <i class="fas fa-arrow-right text-xl"></i>
                </a>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="p-6 space-y-5">
                <div class="flex justify-between items-center pb-3 border-b">
                    <span class="text-gray-600 font-medium"><i class="fas fa-user ml-2 w-6"></i>اسم الوكيل</span>
                    <span class="text-gray-800 font-semibold">{{ $agent->name }}</span>
                </div>
                
                <div class="flex justify-between items-center pb-3 border-b">
                    <span class="text-gray-600 font-medium"><i class="fas fa-user-circle ml-2 w-6"></i>اسم المستخدم</span>
                    <span class="text-gray-800 font-semibold">{{ $agent->username }}</span>
                </div>
                
                <div class="flex justify-between items-center pb-3 border-b">
                    <span class="text-gray-600 font-medium"><i class="fas fa-lock ml-2 w-6"></i>كلمة المرور</span>
                    <span class="text-gray-800 font-mono bg-gray-100 px-3 py-1 rounded">{{ $agent->password }}</span>
                </div>
                
                <div class="flex justify-between items-center pb-3 border-b">
                    <span class="text-gray-600 font-medium"><i class="fas fa-calendar-plus ml-2 w-6"></i>تاريخ الإنشاء</span>
                    <span class="text-gray-800">{{ \Carbon\Carbon::parse($agent->start_date)->format('Y/m/d') }}</span>
                </div>
                
                <div class="flex justify-between items-center pb-3 border-b">
                    <span class="text-gray-600 font-medium"><i class="fas fa-calendar-times ml-2 w-6"></i>تاريخ الانتهاء</span>
                    <span class="text-gray-800 font-semibold">{{ \Carbon\Carbon::parse($agent->end_date)->format('Y/m/d') }}</span>
                </div>
                
                <div class="flex justify-between items-center pb-3 border-b">
                    <span class="text-gray-600 font-medium"><i class="fas fa-hourglass-half ml-2 w-6"></i>الأيام المتبقية</span>
                    <span class="text-lg font-bold">
                        @php
                            $today = new DateTime();
                            $end = new DateTime($agent->end_date);
                            $diff = $today->diff($end)->days;
                            $remaining = $today <= $end ? $diff : -$diff;
                        @endphp
                        @if($remaining < 0)
                            <span class="text-red-600">منتهي</span>
                        @else
                            <span class="text-green-600">{{ $remaining }} يوم متبقي</span>
                        @endif
                    </span>
                </div>
            </div>
            
            <div class="p-6 bg-gray-50 grid grid-cols-2 gap-3">
                <a href="/admin/agent/{{ $agent->id }}/renew" 
                   class="bg-green-600 hover:bg-green-700 text-white font-semibold py-3 rounded-xl transition text-center">
                    <i class="fas fa-sync-alt ml-2"></i> تجديد 30 يوماً
                </a>
                <form action="/admin/agent/{{ $agent->id }}" method="POST" onsubmit="return confirm('هل أنت متأكد من الحذف؟')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-3 rounded-xl transition">
                        <i class="fas fa-trash ml-2"></i> حذف الوكيل
                    </button>
                </form>
                <button onclick="showEditNameModal()" 
                        class="bg-yellow-600 hover:bg-yellow-700 text-white font-semibold py-3 rounded-xl transition">
                    <i class="fas fa-user-edit ml-2"></i> تغيير الاسم
                </button>
                <button onclick="showEditPasswordModal()" 
                        class="bg-purple-600 hover:bg-purple-700 text-white font-semibold py-3 rounded-xl transition">
                    <i class="fas fa-key ml-2"></i> تغيير كلمة المرور
                </button>
            </div>
        </div>
    </div>

    <!-- Modal تعديل الاسم -->
    <div id="editNameModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl w-full max-w-md p-6">
            <h2 class="text-2xl font-bold mb-5">تعديل اسم الوكيل</h2>
            <form action="/admin/agent/{{ $agent->id }}" method="POST">
                @csrf
                @method('PUT')
                <input type="text" name="name" value="{{ $agent->name }}" class="w-full p-3 border rounded-xl" required>
                <div class="flex gap-3 mt-6">
                    <button type="button" onclick="hideEditNameModal()" class="flex-1 py-3 border rounded-xl">إلغاء</button>
                    <button type="submit" class="flex-1 py-3 bg-blue-600 text-white rounded-xl">تعديل</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal تعديل كلمة المرور -->
    <div id="editPasswordModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl w-full max-w-md p-6">
            <h2 class="text-2xl font-bold mb-5">تعديل كلمة المرور</h2>
            <form action="/admin/agent/{{ $agent->id }}" method="POST">
                @csrf
                @method('PUT')
                <input type="text" name="password" value="{{ $agent->password }}" class="w-full p-3 border rounded-xl" required>
                <div class="flex gap-3 mt-6">
                    <button type="button" onclick="hideEditPasswordModal()" class="flex-1 py-3 border rounded-xl">إلغاء</button>
                    <button type="submit" class="flex-1 py-3 bg-blue-600 text-white rounded-xl">تعديل</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function showEditNameModal() {
            document.getElementById('editNameModal').classList.remove('hidden');
        }
        function hideEditNameModal() {
            document.getElementById('editNameModal').classList.add('hidden');
        }
        function showEditPasswordModal() {
            document.getElementById('editPasswordModal').classList.remove('hidden');
        }
        function hideEditPasswordModal() {
            document.getElementById('editPasswordModal').classList.add('hidden');
        }
    </script>

</body>
</html>