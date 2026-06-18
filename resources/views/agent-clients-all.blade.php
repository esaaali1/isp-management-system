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

    <div class="bg-white shadow-lg p-4 flex items-center justify-between sticky top-0 z-10">
        <div>
            <h1 class="text-xl font-bold text-gray-800">جميع المشتركين</h1>
            <p class="text-gray-500 text-xs mt-1" id="agentNameDisplay"></p>
        </div>
        <a href="/agent/dashboard" class="text-blue-600 hover:text-blue-700 text-sm">
            <i class="fas fa-arrow-right ml-1"></i> العودة
        </a>
    </div>

    <div class="p-4 pb-28">
        <div id="clientsList" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3">
        </div>
        <div id="emptyMessage" class="text-center py-12 hidden">
            <i class="fas fa-users text-gray-300 text-5xl mb-2"></i>
            <p class="text-gray-400">لا يوجد مشتركين بعد</p>
        </div>
    </div>

    <button onclick="window.location.href='/agent/dashboard'" 
            class="fixed bottom-6 left-6 w-14 h-14 bg-blue-600 hover:bg-blue-700 text-white rounded-2xl shadow-xl flex items-center justify-center text-2xl transition-all">
        <i class="fas fa-plus"></i>
    </button>

    <script>
        let currentAgent = JSON.parse(sessionStorage.getItem('currentAgent'));
        if (!currentAgent) window.location.href = '/login';
        
        document.getElementById('agentNameDisplay').innerText = currentAgent.name || currentAgent.username;
        
        let clients = JSON.parse(localStorage.getItem(`clients_${currentAgent.username}`)) || [];
        
        const packageColors = {
            'Economy': 'bg-green-100 text-green-600',
            'Standard': 'bg-blue-100 text-blue-600',
            'Business': 'bg-purple-100 text-purple-600'
        };

        function calculateDaysRemaining(endDate) {
            const today = new Date(); today.setHours(0,0,0,0);
            const end = new Date(endDate); end.setHours(0,0,0,0);
            return Math.ceil((end - today) / (1000*60*60*24));
        }

        function formatDate(dateString) {
            try { 
                const d = new Date(dateString);
                return `${d.getFullYear()}/${d.getMonth()+1}/${d.getDate()}`;
            }
            catch(e) { return dateString; }
        }

        function renderClients() {
            const container = document.getElementById('clientsList');
            const emptyMessage = document.getElementById('emptyMessage');
            
            if (clients.length === 0) {
                container.innerHTML = '';
                emptyMessage.classList.remove('hidden');
                return;
            }
            emptyMessage.classList.add('hidden');
            container.innerHTML = '';
            
            clients.forEach((client, index) => {
                const daysRemaining = calculateDaysRemaining(client.end_date);
                let daysColor = 'text-green-600', daysBg = 'bg-green-100';
                if (daysRemaining < 0) { daysColor = 'text-red-600'; daysBg = 'bg-red-100'; }
                else if (daysRemaining < 7) { daysColor = 'text-orange-600'; daysBg = 'bg-orange-100'; }
                
                const card = document.createElement('div');
                card.className = 'bg-white rounded-xl shadow-md p-3 cursor-pointer hover:shadow-lg transition-all';
                card.onclick = () => { window.location.href = `/agent/client/${index}`; };
                card.innerHTML = `
                    <div class="flex justify-between items-start mb-2">
                        <div class="flex-1">
                            <h3 class="text-base font-bold text-gray-800 truncate">${escapeHtml(client.fullname || client.username)}</h3>
                            <p class="text-gray-500 text-xs mt-0.5">${escapeHtml(client.username)}</p>
                            <p class="text-xs mt-1 px-1.5 py-0.5 rounded-full inline-block ${packageColors[client.package] || 'bg-gray-100'}">${client.package || 'غير محدد'}</p>
                        </div>
                        <div class="${daysBg} ${daysColor} px-2 py-0.5 rounded-full text-xs font-semibold whitespace-nowrap mr-2">
                            ${daysRemaining < 0 ? 'منتهي' : daysRemaining + ' يوم'}
                        </div>
                    </div>
                    <div class="flex justify-between text-xs text-gray-400 mt-2 pt-2 border-t">
                        <span><i class="fas fa-calendar-plus ml-1"></i> حتى: ${formatDate(client.end_date)}</span>
                    </div>
                `;
                container.appendChild(card);
            });
        }

        function escapeHtml(text) {
            if (!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        renderClients();
    </script>

</body>
</html>