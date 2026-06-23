<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>المشتركين المنتهية اشتراكاتهم</title>
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
            <h1 class="text-lg font-bold text-gray-800">المنتهى اشتراكهم</h1>
            <p class="text-gray-500 text-xs mt-0.5">{{ $agent->name }}</p>
        </div>
        <a href="/agent/dashboard" class="text-blue-600 hover:text-blue-700 text-sm">
            <i class="fas fa-arrow-right ml-1"></i> العودة
        </a>
    </div>

    <div class="p-3 pb-28">
        <div id="clientsList" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-2">
            @foreach($clients as $client)
            <div onclick="window.location.href='/agent/client/{{ $client->id }}'" 
                 class="bg-white rounded-lg shadow p-2 cursor-pointer hover:shadow-md transition-all">
                <div class="text-center">
                    <div class="flex justify-center mb-1">
                        <div class="bg-red-100 text-red-600 px-1.5 py-0.5 rounded-full text-xs font-semibold">
                            منتهي
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
                        <i class="fas fa-calendar-times ml-1"></i> {{ \Carbon\Carbon::parse($client->end_date)->format('Y/m/d') }}
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        
        @if($clients->isEmpty())
        <div id="emptyMessage" class="text-center py-12">
            <i class="fas fa-calendar-times text-gray-300 text-5xl mb-2"></i>
            <p class="text-gray-400">لا يوجد مشتركين منتهية اشتراكاتهم</p>
        </div>
        @endif
    </div>

    <button onclick="window.location.href='/agent/dashboard'" 
            class="fixed bottom-6 left-6 w-12 h-12 bg-blue-600 hover:bg-blue-700 text-white rounded-xl shadow-xl flex items-center justify-center text-xl transition-all">
        <i class="fas fa-plus"></i>
    </button>

</body>
</html>