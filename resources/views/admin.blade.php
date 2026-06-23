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
        body { font-family: 'Tajawal', sans-serif; }
    </style>
</head>
<body class="bg-gray-100 min-h-screen">

    <div class="bg-white shadow-lg p-5 flex items-center justify-between sticky top-0 z-10">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">الوكلاء</h1>
            <p class="text-gray-500 text-sm mt-1">إدارة الوكلاء والاشتراكات</p>
        </div>
        <div class="flex items-center gap-3">
            <span class="bg-green-100 text-green-600 px-3 py-1 rounded-full text-sm font-semibold">
                <i class="fas fa-user-shield ml-1"></i> admin@essa
            </span>
        </div>
    </div>

    <div class="p-6 pb-28">
        <div id="agentsList" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($agents as $index => $agent)
            <div onclick="window.location.href='/admin/agent/{{ $agent->id }}'" 
                 class="bg-white rounded-2xl shadow-lg p-5 cursor-pointer hover:shadow-xl transition-all">
                <div class="flex justify-between items-start mb-3">
                    <div>
                        <h3 class="text-xl font-bold text-gray-800">{{ $agent->name }}</h3>
                        <p class="text-gray-500 text-sm mt-1">{{ $agent->username }}</p>
                    </div>
                    <div class="bg-blue-100 text-blue-600 px-3 py-1 rounded-full text-sm font-semibold">
                        @php
                            $today = new DateTime();
                            $end = new DateTime($agent->end_date);
                            $diff = $today->diff($end)->days;
                            $remaining = $today <= $end ? $diff : -$diff;
                        @endphp
                        @if($remaining < 0)
                            منتهي
                        @else
                            {{ $remaining }} يوم متبقي
                        @endif
                    </div>
                </div>
                <div class="flex justify-between text-sm text-gray-400 mt-4 pt-3 border-t">
                    <span><i class="fas fa-calendar-plus ml-1"></i> الاشتراك: {{ \Carbon\Carbon::parse($agent->end_date)->format('Y/m/d') }}</span>
                </div>
            </div>
            @endforeach
        </div>
        
        @if($agents->isEmpty())
        <div id="emptyMessage" class="text-center py-16">
            <i class="fas fa-users text-gray-300 text-6xl mb-3"></i>
            <p class="text-gray-400 text-lg">لا يوجد وكلاء بعد</p>
            <p class="text-gray-400 text-sm">اضغط على زر + لإضافة وكيل جديد</p>
        </div>
        @endif
    </div>

    <button onclick="showAddAgentModal()" 
            class="fixed bottom-8 left-8 w-16 h-16 bg-blue-600 hover:bg-blue-700 text-white rounded-2xl shadow-xl flex items-center justify-center text-3xl transition-all z-20">
        <i class="fas fa-plus"></i>
    </button>

    <div id="addAgentModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl w-full max-w-md p-6">
            <div class="flex justify-between items-center mb-5">
                <h2 class="text-2xl font-bold text-gray-800">إضافة وكيل جديد</h2>
                <button onclick="hideAddAgentModal()" class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
            </div>
            <form action="{{ route('agents.store') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-gray-700 font-medium mb-2">اسم الوكيل</label>
                        <input type="text" name="name" class="w-full p-3 border rounded-xl" required>
                    </div>
                    <div>
                        <label class="block text-gray-700 font-medium mb-2">اسم المستخدم</label>
                        <input type="text" name="username" class="w-full p-3 border rounded-xl" required>
                    </div>
                    <div>
                        <label class="block text-gray-700 font-medium mb-2">كلمة المرور</label>
                        <input type="text" name="password" class="w-full p-3 border rounded-xl" required>
                    </div>
                </div>
                <div class="flex gap-3 mt-8">
                    <button type="button" onclick="hideAddAgentModal()" class="flex-1 py-3 border rounded-xl">إلغاء</button>
                    <button type="submit" class="flex-1 py-3 bg-blue-600 text-white rounded-xl">إضافة الوكيل</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function showAddAgentModal() {
            document.getElementById('addAgentModal').classList.remove('hidden');
        }
        function hideAddAgentModal() {
            document.getElementById('addAgentModal').classList.add('hidden');
        }
    </script>

</body>
</html>