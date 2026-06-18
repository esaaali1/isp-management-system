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
        <div id="agentsList" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5"></div>
        <div id="emptyMessage" class="text-center py-16">
            <i class="fas fa-users text-gray-300 text-6xl mb-3"></i>
            <p class="text-gray-400 text-lg">لا يوجد وكلاء بعد</p>
            <p class="text-gray-400 text-sm">اضغط على زر + لإضافة وكيل جديد</p>
        </div>
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
            <div class="space-y-4">
                <div>
                    <label class="block text-gray-700 font-medium mb-2">اسم الوكيل</label>
                    <input type="text" id="agent_name" class="w-full p-3 border rounded-xl">
                </div>
                <div>
                    <label class="block text-gray-700 font-medium mb-2">اسم المستخدم</label>
                    <input type="text" id="agent_username" class="w-full p-3 border rounded-xl">
                </div>
                <div>
                    <label class="block text-gray-700 font-medium mb-2">كلمة المرور</label>
                    <input type="text" id="agent_password" class="w-full p-3 border rounded-xl">
                </div>
            </div>
            <div class="flex gap-3 mt-8">
                <button onclick="hideAddAgentModal()" class="flex-1 py-3 border rounded-xl">إلغاء</button>
                <button onclick="addAgent()" class="flex-1 py-3 bg-blue-600 text-white rounded-xl">إضافة الوكيل</button>
            </div>
        </div>
    </div>

    <script>
        let agents = JSON.parse(localStorage.getItem('agents')) || [];

        function saveAgents() { localStorage.setItem('agents', JSON.stringify(agents)); }

        function calculateDaysRemaining(endDate) {
            const today = new Date(); today.setHours(0,0,0,0);
            const end = new Date(endDate); end.setHours(0,0,0,0);
            return Math.ceil((end - today) / (1000*60*60*24));
        }

        function formatDate(dateString) {
            try { return new Date(dateString).toLocaleDateString('ar-EG'); }
            catch(e) { return dateString; }
        }

        function renderAgents() {
            const container = document.getElementById('agentsList');
            const emptyMessage = document.getElementById('emptyMessage');
            if (agents.length === 0) {
                container.innerHTML = '';
                emptyMessage.classList.remove('hidden');
                return;
            }
            emptyMessage.classList.add('hidden');
            container.innerHTML = '';
            agents.forEach((agent, index) => {
                const daysRemaining = calculateDaysRemaining(agent.end_date);
                let daysColor = 'text-green-600', daysBg = 'bg-green-100';
                if (daysRemaining < 0) { daysColor = 'text-red-600'; daysBg = 'bg-red-100'; }
                else if (daysRemaining < 7) { daysColor = 'text-orange-600'; daysBg = 'bg-orange-100'; }
                const card = document.createElement('div');
                card.className = 'bg-white rounded-2xl shadow-lg p-5 cursor-pointer hover:shadow-xl transition-all';
                card.onclick = () => { window.location.href = `/admin/agent/${index}`; };  // <-- التصحيح هنا
                card.innerHTML = `
                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <h3 class="text-xl font-bold text-gray-800">${escapeHtml(agent.name)}</h3>
                            <p class="text-gray-500 text-sm mt-1">${escapeHtml(agent.username)}</p>
                        </div>
                        <div class="${daysBg} ${daysColor} px-3 py-1 rounded-full text-sm font-semibold">
                            ${daysRemaining < 0 ? 'منتهي' : daysRemaining + ' يوم متبقي'}
                        </div>
                    </div>
                    <div class="flex justify-between text-sm text-gray-400 mt-4 pt-3 border-t">
                        <span><i class="fas fa-calendar-plus ml-1"></i> الاشتراك: ${formatDate(agent.end_date)}</span>
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

        function addAgent() {
            const name = document.getElementById('agent_name').value.trim();
            const username = document.getElementById('agent_username').value.trim();
            const password = document.getElementById('agent_password').value.trim();
            if (!name || !username || !password) { alert('يرجى ملء جميع الحقول'); return; }
            const end_date = new Date();
            end_date.setDate(end_date.getDate() + 30);
            agents.push({ name, username, password, start_date: new Date().toISOString().split('T')[0], end_date: end_date.toISOString().split('T')[0] });
            saveAgents();
            renderAgents();
            hideAddAgentModal();
            document.getElementById('agent_name').value = '';
            document.getElementById('agent_username').value = '';
            document.getElementById('agent_password').value = '';
        }

        function showAddAgentModal() { document.getElementById('addAgentModal').classList.remove('hidden'); }
        function hideAddAgentModal() { document.getElementById('addAgentModal').classList.add('hidden'); }

        renderAgents();
    </script>
</body>
</html>