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
        body { font-family: 'Tajawal', sans-serif; background-color: #f0f2f5; }
        .card-hover {
            transition: all 0.2s ease-in-out;
        }
        .card-hover:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 20px -10px rgba(0,0,0,0.1);
        }
        .search-result-hidden {
            display: none !important;
        }
    </style>
</head>
<body class="min-h-screen">

    <!-- Header & Search -->
    <div class="bg-[#f0f2f5] px-6 py-6 flex flex-col-reverse md:flex-row items-center justify-between sticky top-0 z-10 gap-4">
        
        <!-- مربع البحث (تم إضافة id و oninput) -->
        <div class="relative w-full md:w-[350px]">
            <input type="text" id="searchInput" oninput="filterClients()" placeholder="ابحث عن مشتركين..." 
                   class="w-full pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:outline-none focus:border-gray-400 focus:ring-2 focus:ring-gray-100 shadow-sm text-right text-gray-700 placeholder-gray-400 font-medium text-sm">
            <i class="fas fa-search absolute left-4 top-3.5 text-gray-400 text-md"></i>
        </div>

        <!-- العنوان واسم الوكيل -->
        <div class="text-center md:text-right w-full md:w-auto">
            <h1 class="text-2xl font-bold text-gray-900">جميع المشتركين</h1>
            <p class="text-gray-500 text-sm mt-1">{{ $agent->name ?? 'محمد' }}</p>
        </div>
    </div>

    <!-- رسائل التنبيهات -->
    @if (session('success'))
        <div class="mx-6 mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="mx-6 mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm">
            {{ session('error') }}
        </div>
    @endif
    @if ($errors->any())
        <div class="mx-6 mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- قائمة المشتركين -->
    <div class="px-6 pb-28">
        <!-- التعديل هنا: lg:grid-cols-5 لضمان 5 بطاقات في شاشات اللاب توب مع تقليل المسافة (gap-3) -->
        <div id="clientsContainer" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3 md:gap-4">
            @forelse($clients as $client)
            
            @php
                // حساب الأيام المتبقية
                $today = new DateTime();
                $end = new DateTime($client->end_date);
                $diff = $today->diff($end)->days;
                $remaining = $today <= $end ? $diff : -$diff;

                // إعدادات ألوان الباقات بناءً على التصميم
                $pkgBg = 'bg-gray-200'; $pkgText = 'text-gray-800'; $ringColor = 'border-gray-300';
                
                $packageName = strtolower($client->package);
                if(str_contains($packageName, 'economy')) {
                    $pkgBg = 'bg-[#315743]'; $pkgText = 'text-white'; $ringColor = 'border-[#315743]';
                } elseif(str_contains($packageName, 'business')) {
                    $pkgBg = 'bg-[#4b3c5a]'; $pkgText = 'text-white'; $ringColor = 'border-[#4b3c5a]';
                } elseif(str_contains($packageName, 'standard')) {
                    $pkgBg = 'bg-[#d2e5f5]'; $pkgText = 'text-gray-800'; $ringColor = 'border-[#92c5e9]';
                } elseif(str_contains($packageName, 'premium')) {
                    $pkgBg = 'bg-[#c29d44]'; $pkgText = 'text-white'; $ringColor = 'border-[#c29d44]';
                } else {
                    $pkgBg = 'bg-gray-200'; $pkgText = 'text-gray-800'; $ringColor = 'border-gray-300';
                }
            @endphp

            <!-- تم إضافة data-name و data-username لتسهيل البحث -->
            <div onclick="window.location.href='/agent/client/{{ $client->id }}'" 
                 class="client-card bg-[#f8f9fb] rounded-2xl p-3 shadow-sm border border-gray-200 cursor-pointer card-hover relative flex flex-col justify-between"
                 data-name="{{ strtolower($client->fullname) }}" 
                 data-username="{{ strtolower($client->username) }}">
                
                <div class="flex justify-between items-start w-full mb-2 gap-1.5">
                    
                    <!-- اليمين: الصورة واسم المشترك -->
                    <div class="flex items-center gap-2 flex-1 min-w-0">
                        <!-- تم تصغير الأفاتار لـ 45px -->
                        <div class="w-[45px] h-[45px] bg-[#e2e4e9] rounded-full flex items-center justify-center flex-shrink-0 relative overflow-hidden">
                            <i class="fas fa-user text-gray-500 text-[35px] absolute -bottom-1.5"></i>
                        </div>

                        <!-- معلومات المشترك (مع دعم الاقتطاع للنصوص الطويلة) -->
                        <div class="flex flex-col items-start mt-0.5 overflow-hidden flex-1 min-w-0">
                            <h3 class="text-[14px] font-bold text-gray-900 leading-tight mb-1 truncate w-full" title="{{ $client->fullname }}">{{ $client->fullname }}</h3>
                            <p class="text-gray-500 text-[10px] font-medium mb-1 truncate w-full">{{ $client->username }}</p>
                            
                            <!-- الباقة -->
                            <div class="px-1.5 py-0.5 rounded-md text-[9px] font-medium flex items-center gap-1 {{ $pkgBg }} {{ $pkgText }} whitespace-nowrap">
                                {{ $client->package }}
                                <i class="fas fa-building text-[7px] opacity-90"></i>
                            </div>
                        </div>
                    </div>

                    <!-- اليسار: الدائرة والأيام المتبقية -->
                    <div class="flex flex-col items-center flex-shrink-0">
                        <!-- الشارة الخضراء العلوية -->
                        <div class="bg-[#e4f1ea] text-[#315743] px-1.5 py-0.5 rounded-full text-[9px] font-bold mb-1">
                            {{ $remaining }} ي
                        </div>
                        
                        <!-- تم تصغير الدائرة لـ 45px -->
                        <div class="w-[45px] h-[45px] rounded-full border-[3px] {{ $ringColor }} flex flex-col items-center justify-center bg-white shadow-sm">
                            <span class="text-[14px] font-bold text-gray-800 leading-none mt-0.5">{{ $remaining }}</span>
                            <span class="text-[7px] text-gray-500 font-medium mt-0.5">باقي أيام</span>
                        </div>
                        <div class="text-[7.5px] text-gray-400 mt-1 font-medium whitespace-nowrap">{{ $remaining }} Days</div>
                    </div>

                </div>

                <!-- الأسفل: تاريخ الانتهاء -->
                <div class="border-t border-gray-200 pt-2 mt-auto flex justify-center items-center text-[11px] font-medium text-gray-700">
                    <span class="ml-1">تاريخ الانتهاء:</span>
                    <i class="fas fa-calendar-alt text-gray-400 mx-1 text-[10px]"></i>
                    <span class="tracking-wider">{{ \Carbon\Carbon::parse($client->end_date)->format('Y/m/d') }}</span>
                </div>
            </div>
            @empty
            <div id="emptyMessage" class="col-span-full text-center py-16 bg-white rounded-2xl border border-gray-200">
                <i class="fas fa-users text-gray-300 text-5xl mb-4"></i>
                <p class="text-gray-500 text-lg font-bold">لا يوجد مشتركين بعد</p>
                <p class="text-gray-400 text-sm mt-2">اضغط على زر + لإضافة مشترك جديد</p>
            </div>
            @endforelse
        </div>
        <!-- رسالة "لا توجد نتائج" التي ستظهر عند عدم وجود مطابقات -->
        <div id="noResultsMessage" class="hidden col-span-full text-center py-16 bg-white rounded-2xl border border-gray-200 mt-4">
            <i class="fas fa-search text-gray-300 text-5xl mb-4"></i>
            <p class="text-gray-500 text-lg font-bold">لا توجد نتائج مطابقة</p>
            <p class="text-gray-400 text-sm mt-2">جرب البحث بكلمة أخرى</p>
        </div>
    </div>

    <!-- زر + (إضافة مشترك) -->
    <button onclick="showAddClientModal()" 
            class="fixed bottom-6 left-6 w-12 h-12 bg-blue-600 hover:bg-blue-700 text-white rounded-xl shadow-lg shadow-blue-200 flex items-center justify-center text-xl transition-all z-20">
        <i class="fas fa-plus"></i>
    </button>

    <!-- Modal إضافة مشترك -->
    <div id="addClientModal" class="hidden fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50 p-4 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-2xl w-full max-w-md p-6 shadow-2xl">
            <div class="flex justify-between items-center mb-5">
                <h2 class="text-xl font-bold text-gray-800">إضافة مشترك جديد</h2>
                <button onclick="hideAddClientModal()" class="text-gray-400 hover:text-gray-600 text-2xl leading-none">&times;</button>
            </div>
            
            <form action="{{ route('clients.store') }}" method="POST">
                @csrf
                <!-- ✅ التعديل: استخدام $agent->id ديناميكياً -->
                <input type="hidden" name="agent_id" value="{{ $agent->id ?? '' }}">
                
                <div class="space-y-3">
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-1.5">اسم المشترك</label>
                        <input type="text" name="fullname" class="w-full p-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:bg-white outline-none transition-all text-sm" required>
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-1.5">اسم المستخدم (PPPoE)</label>
                        <input type="text" name="username" class="w-full p-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:bg-white outline-none transition-all text-sm" required>
                    </div>
                    
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-1.5">كلمة المرور</label>
                        <input type="text" name="password" class="w-full p-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:bg-white outline-none transition-all text-sm" required>
                    </div>
                    
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-1.5">نوع الباقة</label>
                        <select name="package" class="w-full p-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:bg-white outline-none transition-all text-sm" required>
                            <option value="Economy">Economy (اقتصادي)</option>
                            <option value="Standard">Standard (قياسي)</option>
                            <option value="Business">Business (أعمال)</option>
                            <option value="Premium">Premium (مميز)</option>
                        </select>
                    </div>
                </div>

                <div class="flex gap-3 mt-6">
                    <button type="button" onclick="hideAddClientModal()" class="flex-1 py-3 border border-gray-200 bg-gray-50 rounded-xl font-bold text-gray-700 text-sm hover:bg-gray-100 transition">إلغاء</button>
                    <button type="submit" class="flex-1 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold text-sm shadow-md shadow-blue-200 transition">إضافة المشترك</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // دالة البحث
        function filterClients() {
            const input = document.getElementById('searchInput');
            const filter = input.value.toLowerCase().trim();
            const cards = document.querySelectorAll('.client-card');
            const noResults = document.getElementById('noResultsMessage');
            let found = false;

            cards.forEach(card => {
                const name = card.dataset.name || '';
                const username = card.dataset.username || '';
                // البحث في الاسم أو اسم المستخدم
                if (name.includes(filter) || username.includes(filter)) {
                    card.classList.remove('search-result-hidden');
                    found = true;
                } else {
                    card.classList.add('search-result-hidden');
                }
            });

            // إظهار رسالة "لا توجد نتائج" إذا لم يتم العثور على شيء
            if (found || filter === '') {
                noResults.classList.add('hidden');
            } else {
                noResults.classList.remove('hidden');
            }
        }

        // دوال إظهار وإخفاء الـ Modal
        function showAddClientModal() {
            document.getElementById('addClientModal').classList.remove('hidden');
        }
        function hideAddClientModal() {
            document.getElementById('addClientModal').classList.add('hidden');
        }
    </script>

</body>
</html>