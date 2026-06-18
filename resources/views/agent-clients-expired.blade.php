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

    <div class="bg-white shadow-lg p-5 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">المنتهى اشتراكهم</h1>
            <p class="text-gray-500 text-sm mt-1" id="agentNameDisplay"></p>
        </div>
        <a href="/agent/dashboard" class="text-blue-600 hover:text-blue-700">
            <i class="fas fa-arrow-right text-xl"></i> العودة
        </a>
    </div>

    <div class="p-6 pb-28">
        <div id="clientsList" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5"></div>
        <div id="emptyMessage" class="text-center py-16 hidden">
            <i class="fas fa-calendar-times text-gray-300 text-6xl mb-3"></i>
            <p class="text-gray-400 text-lg">لا يوجد مشتركين منتهية اشتراكاتهم</p>
        </div>
    </div>

    <button onclick="window.location.href='/agent/dashboard'" 
            class="fixed bottom-8 left-8 w-16 h-16 bg-blue-600 hover:bg-blue-700 text-white rounded-2xl shadow-xl flex items-center justify-center text-3xl transition-all">
        <i class="fas fa-plus"></i>
    </button>

    <script>
        let currentAgent = JSON.parse(sessionStorage.getItem('currentAgent'));
        if (!currentAgent) window.location.href = '/login';
        
        document.getElementById('agentNameDisplay').innerText = currentAgent.name || currentAgent.username;
        
        let allClients = JSON.parse(localStorage.getItem(`clients_${currentAgent.username}`)) || [];
        let clients = allClients.filter(client => {
            const today = new Date(); today.setHours(0,0,0,0);
            const end = new Date(client.end_date); end.setHours(0,0,0,0);
            return (end - today) < 0;
        });
        
        const packageColors = {
            'Economy': 'bg-green-100 text-green-600',
            'Standard': 'bg-blue-100 text-blue-600',
            'Business': 'bg-purple-100 text-purple-600'
        };

        function formatDate(dateString) {
            try { return new Date(dateString).toLocaleDateString('ar-EG'); }
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
                const originalIndex = allClients.findIndex(c => c.username === client.username);
                const card = document.createElement('div');
                card.className = 'bg-white rounded-2xl shadow-lg p-5 cursor-pointer hover:shadow-xl transition-all';
                card.onclick = () => { window.location.href = `/agent/client/${originalIndex}`; };
                card.innerHTML = `
                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <h3 class="text-xl font-bold text-gray-800">${escapeHtml(client.fullname || client.username)}</h3>
                            <p class="text-gray-500 text-sm mt-1">${escapeHtml(client.username)}</p>
                            <p class="text-sm mt-1 px-2 py-0.5 rounded-full inline-block ${packageColors[client.package]}">${client.package}</p>
                        </div>
                        <div class="bg-red-100 text-red-600 px-3 py-1 rounded-full text-sm font-semibold">
                            منتهي
                        </div>
                    </div>
                    <div class="text-sm text-gray-400 mt-4 pt-3 border-t">
                        <i class="fas fa-calendar-times ml-1"></i> انتهى في: ${formatDate(client.end_date)}
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