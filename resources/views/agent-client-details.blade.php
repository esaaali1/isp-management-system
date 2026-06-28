<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تفاصيل المشترك</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Tajawal', sans-serif; }
    </style>
</head>
<body class="bg-gray-100 min-h-screen">

    <div class="container mx-auto p-4 max-w-2xl">
        <!-- Header -->
        <div class="bg-white shadow-lg rounded-2xl p-5 mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">{{ $client->fullname }}</h1>
                    <p class="text-gray-500 text-sm mt-1">تفاصيل المشترك وحالة الاشتراك</p>
                </div>
                <a href="/agent/clients/all/{{ $client->agent_id }}" class="text-blue-600 hover:text-blue-700">
                    <i class="fas fa-arrow-right text-xl"></i> العودة
                </a>
            </div>
        </div>

        <!-- بطاقة التفاصيل -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="p-6 space-y-5">
                <!-- اسم المشترك -->
                <div class="flex justify-between items-center pb-3 border-b">
                    <span class="text-gray-600 font-medium"><i class="fas fa-user-circle ml-2 w-6"></i>اسم المشترك</span>
                    <span class="text-gray-800 font-semibold">{{ $client->fullname }}</span>
                </div>

                <!-- اسم المستخدم -->
                <div class="flex justify-between items-center pb-3 border-b">
                    <span class="text-gray-600 font-medium"><i class="fas fa-user ml-2 w-6"></i>اسم المستخدم</span>
                    <span class="text-gray-800 font-semibold">{{ $client->username }}</span>
                </div>
                
                <!-- كلمة المرور -->
                <div class="flex justify-between items-center pb-3 border-b">
                    <span class="text-gray-600 font-medium"><i class="fas fa-lock ml-2 w-6"></i>كلمة المرور</span>
                    <span class="text-gray-800 font-mono bg-gray-100 px-3 py-1 rounded">{{ $client->password }}</span>
                </div>
                
                <!-- نوع الباقة -->
                <div class="flex justify-between items-center pb-3 border-b">
                    <span class="text-gray-600 font-medium"><i class="fas fa-tag ml-2 w-6"></i>نوع الباقة</span>
                    <span class="px-3 py-1 rounded-full text-sm font-semibold 
                        @if($client->package == 'Economy') bg-green-100 text-green-600
                        @elseif($client->package == 'Standard') bg-blue-100 text-blue-600
                        @else bg-purple-100 text-purple-600 @endif">
                        {{ $client->package }}
                    </span>
                </div>
                
                <!-- تاريخ الإنشاء -->
                <div class="flex justify-between items-center pb-3 border-b">
                    <span class="text-gray-600 font-medium"><i class="fas fa-calendar-plus ml-2 w-6"></i>تاريخ الإنشاء</span>
                    <span class="text-gray-800">{{ \Carbon\Carbon::parse($client->start_date)->format('Y/m/d') }}</span>
                </div>
                
                <!-- تاريخ الانتهاء -->
                <div class="flex justify-between items-center pb-3 border-b">
                    <span class="text-gray-600 font-medium"><i class="fas fa-calendar-times ml-2 w-6"></i>تاريخ الانتهاء</span>
                    <span class="text-gray-800 font-semibold">{{ \Carbon\Carbon::parse($client->end_date)->format('Y/m/d') }}</span>
                </div>

                <!-- عنوان IP الدخول (الحقل الجديد) -->
                <div class="flex justify-between items-center pb-3 border-b">
                    <span class="text-gray-600 font-medium"><i class="fas fa-network-wired ml-2 w-6"></i>عنوان IP الدخول</span>
                    @if($clientIp)
                        <a href="http://{{ $clientIp }}" target="_blank" 
                           class="text-blue-600 hover:text-blue-800 font-mono bg-gray-100 px-3 py-1 rounded cursor-pointer transition hover:bg-gray-200">
                            {{ $clientIp }}
                            <i class="fas fa-external-link-alt mr-1 text-xs"></i>
                        </a>
                    @else
                        <span class="text-gray-400 font-medium">غير متصل</span>
                    @endif
                </div>
                
                <!-- الأيام المتبقية -->
                <div class="flex justify-between items-center pb-3 border-b">
                    <span class="text-gray-600 font-medium"><i class="fas fa-hourglass-half ml-2 w-6"></i>الأيام المتبقية</span>
                    <span class="text-lg font-bold">
                        @php
                            $today = new DateTime();
                            $end = new DateTime($client->end_date);
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
            
            <!-- الأزرار -->
            <div class="p-6 bg-gray-50 grid grid-cols-2 gap-3">
                <a href="/agent/client/{{ $client->id }}/renew" 
                   class="bg-green-600 hover:bg-green-700 text-white font-semibold py-3 rounded-xl transition text-center">
                    <i class="fas fa-sync-alt ml-2"></i> تجديد 30 يوماً
                </a>
                <form action="/agent/client/{{ $client->id }}" method="POST" onsubmit="return confirm('هل أنت متأكد من الحذف؟')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-3 rounded-xl transition">
                        <i class="fas fa-trash ml-2"></i> حذف المشترك
                    </button>
                </form>
                <button onclick="showEditPackageModal()" 
                        class="bg-purple-600 hover:bg-purple-700 text-white font-semibold py-3 rounded-xl transition">
                    <i class="fas fa-exchange-alt ml-2"></i> تغيير الباقة
                </button>
                <button onclick="showEditDateModal()" 
                        class="bg-yellow-600 hover:bg-yellow-700 text-white font-semibold py-3 rounded-xl transition">
                    <i class="fas fa-calendar-edit ml-2"></i> تعديل التاريخ
                </button>
            </div>
        </div>
    </div>

    <!-- Modal تعديل الباقة -->
    <div id="editPackageModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl w-full max-w-md p-6">
            <h2 class="text-2xl font-bold mb-5">تغيير الباقة</h2>
            <form action="/agent/client/{{ $client->id }}" method="POST">
                @csrf
                @method('PUT')
                <select name="package" class="w-full p-3 border rounded-xl">
                    <option value="Economy" @if($client->package == 'Economy') selected @endif>Economy</option>
                    <option value="Standard" @if($client->package == 'Standard') selected @endif>Standard</option>
                    <option value="Business" @if($client->package == 'Business') selected @endif>Business</option>
                </select>
                <div class="flex gap-3 mt-6">
                    <button type="button" onclick="hideEditPackageModal()" class="flex-1 py-3 border rounded-xl">إلغاء</button>
                    <button type="submit" class="flex-1 py-3 bg-blue-600 text-white rounded-xl">تعديل</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal تعديل التاريخ -->
    <div id="editDateModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl w-full max-w-md p-6">
            <h2 class="text-2xl font-bold mb-5">تعديل تاريخ الانتهاء</h2>
            <form action="/agent/client/{{ $client->id }}" method="POST">
                @csrf
                @method('PUT')
                <input type="date" name="end_date" value="{{ $client->end_date }}" class="w-full p-3 border rounded-xl" required>
                <div class="flex gap-3 mt-6">
                    <button type="button" onclick="hideEditDateModal()" class="flex-1 py-3 border rounded-xl">إلغاء</button>
                    <button type="submit" class="flex-1 py-3 bg-blue-600 text-white rounded-xl">تعديل</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function showEditPackageModal() {
            document.getElementById('editPackageModal').classList.remove('hidden');
        }
        function hideEditPackageModal() {
            document.getElementById('editPackageModal').classList.add('hidden');
        }
        function showEditDateModal() {
            document.getElementById('editDateModal').classList.remove('hidden');
        }
        function hideEditDateModal() {
            document.getElementById('editDateModal').classList.add('hidden');
        }
    </script>

</body>
</html>