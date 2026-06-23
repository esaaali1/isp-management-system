<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>جميع المشتركين</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Tajawal', sans-serif; }
    </style>
</head>
<body class="bg-gray-100 min-h-screen">

    <div class="bg-white shadow-lg p-3 flex items-center justify-between sticky top-0 z-10">
        <div>
            <h1 class="text-lg font-bold text-gray-800">جميع المشتركين</h1>
            <p class="text-gray-500 text-xs mt-0.5">{{ $agent->name ?? '' }}</p>
        </div>
        <a href="/agent/dashboard" class="text-blue-600 hover:text-blue-700 text-sm">
            <i class="fas fa-arrow-right ml-1"></i> العودة
        </a>
    </div>

    <!-- عرض رسائل النجاح أو الخطأ -->
    @if (session('success'))
        <div class="mx-3 mt-3 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mx-3 mt-3 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
            {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mx-3 mt-3 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="p-3 pb-28">
        <div id="clientsList" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-2">
            @foreach($clients as $client)
            <div onclick="window.location.href='/agent/client/{{ $client->id }}'" 
                 class="bg-white rounded-lg shadow p-2 cursor-pointer hover:shadow-md transition-all">
                <div class="text-center">
                    <div class="flex justify-center mb-1">
                        @php
                            $today = new DateTime();
                            $end = new DateTime($client->end_date);
                            $diff = $today->diff($end)->days;
                            $remaining = $today <= $end ? $diff : -$diff;
                        @endphp
                        <div class="px-1.5 py-0.5 rounded-full text-xs font-semibold 
                            @if($remaining < 0) bg-red-100 text-red-600
                            @elseif($remaining < 7) bg-orange-100 text-orange-600
                            @else bg-green-100 text-green-600 @endif">
                            {{ $remaining < 0 ? 'منتهي' : $remaining . ' ي' }}
                        </div>
                    </div>
                    <h3 class="text-base font-bold text-gray-800 truncate">{{ $client->fullname }}</h3>
                    <p class="text-gray-500 text-xs truncate">{{ $client->username }}</p>
                    <p class="text-xs mt-1 px-1 py-0.5 rounded-full inline-block 
                        @if($client->package == 'Economy') bg-green-100 text-green-600
                        @elseif($client->package == 'Standard') bg-blue-100 text-blue-600
                        @else bg-purple-100 text-purple-600 @endif">
                        {{ $client->package }}
                    </p>
                    <div class="text-xs text-gray-400 mt-1 pt-1 border-t">
                        <i class="fas fa-calendar-plus ml-1"></i> {{ \Carbon\Carbon::parse($client->end_date)->format('Y/m/d') }}
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        
        @if($clients->isEmpty())
        <div id="emptyMessage" class="text-center py-12">
            <i class="fas fa-users text-gray-300 text-5xl mb-2"></i>
            <p class="text-gray-400">لا يوجد مشتركين بعد</p>
        </div>
        @endif
    </div>

    <!-- زر + (يفتح النموذج) -->
    <button onclick="showAddClientModal()" 
            class="fixed bottom-6 left-6 w-12 h-12 bg-blue-600 hover:bg-blue-700 text-white rounded-xl shadow-xl flex items-center justify-center text-xl transition-all z-20">
        <i class="fas fa-plus"></i>
    </button>

    <!-- نموذج إضافة مشترك (Modal) -->
    <div id="addClientModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl w-full max-w-md p-6">
            <div class="flex justify-between items-center mb-5">
                <h2 class="text-2xl font-bold text-gray-800">إضافة مشترك جديد</h2>
                <button onclick="hideAddClientModal()" class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
            </div>
            
            <form action="{{ route('clients.store') }}" method="POST">
                @csrf
                <input type="hidden" name="agent_id" value="{{ $agent->id ?? '' }}">
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-gray-700 font-medium mb-2">اسم المشترك</label>
                        <input type="text" name="fullname" class="w-full p-3 border rounded-xl" required>
                    </div>

                    <div>
                        <label class="block text-gray-700 font-medium mb-2">اسم المستخدم (PPPoE)</label>
                        <input type="text" name="username" class="w-full p-3 border rounded-xl" required>
                    </div>
                    
                    <div>
                        <label class="block text-gray-700 font-medium mb-2">كلمة المرور</label>
                        <input type="text" name="password" class="w-full p-3 border rounded-xl" required>
                    </div>
                    
                    <div>
                        <label class="block text-gray-700 font-medium mb-2">نوع الباقة</label>
                        <select name="package" class="w-full p-3 border rounded-xl" required>
                            <option value="Economy">Economy</option>
                            <option value="Standard">Standard</option>
                            <option value="Business">Business</option>
                        </select>
                    </div>
                </div>

                <div class="flex gap-3 mt-8">
                    <button type="button" onclick="hideAddClientModal()" class="flex-1 py-3 border rounded-xl">إلغاء</button>
                    <button type="submit" class="flex-1 py-3 bg-blue-600 text-white rounded-xl">إضافة المشترك</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function showAddClientModal() {
            document.getElementById('addClientModal').classList.remove('hidden');
        }
        function hideAddClientModal() {
            document.getElementById('addClientModal').classList.add('hidden');
        }
    </script>

</body>
</html>