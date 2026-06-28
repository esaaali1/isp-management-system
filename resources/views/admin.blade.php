<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة الإدارة - الوكلاء</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Tajawal', sans-serif; background-color: #f0f2f5; }
        .card-hover { transition: all 0.2s ease-in-out; }
        .card-hover:hover { transform: translateY(-4px); box-shadow: 0 10px 20px -10px rgba(0,0,0,0.1); }
    </style>
</head>
<body class="min-h-screen">

    <!-- Header -->
    <div class="bg-[#f0f2f5] px-6 py-6 flex items-center justify-between sticky top-0 z-10">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">الوكلاء</h1>
            <p class="text-gray-500 text-sm mt-1">إدارة الوكلاء والاشتراكات</p>
        </div>
        <div class="bg-white px-4 py-2 rounded-full shadow-sm border border-gray-200 text-green-600 text-sm font-bold">
            <i class="fas fa-user-shield ml-1"></i> admin@essa
        </div>
    </div>

    <!-- قائمة الوكلاء -->
    <div class="px-6 pb-28">
        <div id="agentsList" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3 md:gap-4">
            @foreach($agents as $agent)
            @php
                $today = new DateTime();
                $end = new DateTime($agent->end_date);
                $diff = $today->diff($end)->days;
                $remaining = $today <= $end ? $diff : -$diff;
            @endphp
            
            <div onclick="window.location.href='/admin/agent/{{ $agent->id }}'" 
                 class="bg-[#f8f9fb] rounded-2xl p-3 shadow-sm border border-gray-200 cursor-pointer card-hover flex flex-col justify-between">
                
                <div class="flex justify-between items-start mb-2 gap-1.5">
                    <div class="flex items-center gap-2 flex-1 min-w-0">
                        <div class="w-[45px] h-[45px] bg-[#e2e4e9] rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-user-tie text-gray-500 text-[22px]"></i>
                        </div>
                        <div class="flex flex-col items-start overflow-hidden flex-1">
                            <h3 class="text-[14px] font-bold text-gray-900 truncate w-full">{{ $agent->name }}</h3>
                            <p class="text-gray-500 text-[10px] truncate w-full">{{ $agent->username }}</p>
                        </div>
                    </div>

                    <div class="flex flex-col items-center">
                        <div class="px-2 py-0.5 rounded-full text-[9px] font-bold {{ $remaining < 0 ? 'bg-red-100 text-red-600' : 'bg-green-100 text-green-600' }}">
                            {{ $remaining < 0 ? 'منتهي' : $remaining . ' ي' }}
                        </div>
                    </div>
                </div>

                <div class="border-t border-gray-200 pt-2 mt-2 text-[11px] text-gray-600 font-medium flex justify-center items-center">
                    <i class="fas fa-calendar-alt ml-1 text-gray-400"></i>
                    {{ \Carbon\Carbon::parse($agent->end_date)->format('Y/m/d') }}
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- زر الإضافة -->
    <button onclick="showAddAgentModal()" 
            class="fixed bottom-6 left-6 w-12 h-12 bg-blue-600 hover:bg-blue-700 text-white rounded-xl shadow-lg flex items-center justify-center text-xl z-20">
        <i class="fas fa-plus"></i>
    </button>

    <!-- Modal إضافة وكيل -->
    <div id="addAgentModal" class="hidden fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl w-full max-w-md p-6 shadow-2xl overflow-y-auto max-h-[90vh]">
            <h2 class="text-xl font-bold mb-5">إضافة وكيل جديد</h2>
            <form action="{{ route('agents.store') }}" method="POST">
                @csrf
                <div class="space-y-3">
                    <input type="text" name="name" placeholder="اسم الوكيل" class="w-full p-3 bg-gray-50 border rounded-xl text-sm" required>
                    <input type="text" name="username" placeholder="اسم المستخدم" class="w-full p-3 bg-gray-50 border rounded-xl text-sm" required>
                    <input type="text" name="password" placeholder="كلمة المرور" class="w-full p-3 bg-gray-50 border rounded-xl text-sm" required>
                    <hr>
                    <input type="text" name="mikrotik_host" placeholder="IP المايكروتيك" class="w-full p-3 bg-gray-50 border rounded-xl text-sm">
                    <input type="text" name="mikrotik_user" placeholder="اسم مستخدم المايكروتيك" class="w-full p-3 bg-gray-50 border rounded-xl text-sm">
                    <input type="text" name="mikrotik_pass" placeholder="كلمة مرور المايكروتيك" class="w-full p-3 bg-gray-50 border rounded-xl text-sm">
                    <input type="number" name="mikrotik_port" placeholder="المنفذ (Port)" class="w-full p-3 bg-gray-50 border rounded-xl text-sm" value="8728">
                </div>
                <div class="flex gap-3 mt-6">
                    <button type="button" onclick="hideAddAgentModal()" class="flex-1 py-3 border rounded-xl text-sm">إلغاء</button>
                    <button type="submit" class="flex-1 py-3 bg-blue-600 text-white rounded-xl text-sm">إضافة الوكيل</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function showAddAgentModal() { document.getElementById('addAgentModal').classList.remove('hidden'); }
        function hideAddAgentModal() { document.getElementById('addAgentModal').classList.add('hidden'); }
    </script>
</body>
</html>