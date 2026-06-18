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
                    <h1 class="text-2xl font-bold text-gray-800" id="agentName"></h1>
                    <p class="text-gray-500 text-sm mt-1">تفاصيل الوكيل وحالة الاشتراك</p>
                </div>
                <a href="/admin" class="text-blue-600 hover:text-blue-700">
                    <i class="fas fa-arrow-right text-xl"></i>
                </a>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="p-6 space-y-5">
                <!-- اسم الوكيل (بدون زر تعديل) -->
                <div class="flex justify-between items-center pb-3 border-b">
                    <span class="text-gray-600 font-medium"><i class="fas fa-user ml-2 w-6"></i>اسم الوكيل</span>
                    <span class="text-gray-800 font-semibold" id="detailsName"></span>
                </div>
                
                <!-- اسم المستخدم -->
                <div class="flex justify-between items-center pb-3 border-b">
                    <span class="text-gray-600 font-medium"><i class="fas fa-user-circle ml-2 w-6"></i>اسم المستخدم</span>
                    <span class="text-gray-800 font-semibold" id="detailsUsername"></span>
                </div>
                
                <!-- كلمة المرور (بدون زر تعديل) -->
                <div class="flex justify-between items-center pb-3 border-b">
                    <span class="text-gray-600 font-medium"><i class="fas fa-lock ml-2 w-6"></i>كلمة المرور</span>
                    <span class="text-gray-800 font-mono bg-gray-100 px-3 py-1 rounded" id="detailsPassword"></span>
                </div>
                
                <!-- تاريخ الإنشاء -->
                <div class="flex justify-between items-center pb-3 border-b">
                    <span class="text-gray-600 font-medium"><i class="fas fa-calendar-plus ml-2 w-6"></i>تاريخ الإنشاء</span>
                    <span class="text-gray-800" id="detailsStartDate"></span>
                </div>
                
                <!-- تاريخ الانتهاء -->
                <div class="flex justify-between items-center pb-3 border-b">
                    <span class="text-gray-600 font-medium"><i class="fas fa-calendar-times ml-2 w-6"></i>تاريخ الانتهاء</span>
                    <span class="text-gray-800 font-semibold" id="detailsEndDate"></span>
                </div>
                
                <!-- الأيام المتبقية -->
                <div class="flex justify-between items-center pb-3 border-b">
                    <span class="text-gray-600 font-medium"><i class="fas fa-hourglass-half ml-2 w-6"></i>الأيام المتبقية</span>
                    <span class="text-lg font-bold" id="detailsDaysLeft"></span>
                </div>
            </div>
            
            <!-- 4 أزرار كبيرة -->
            <div class="p-6 bg-gray-50 grid grid-cols-2 gap-3">
                <button onclick="renewAgent()" 
                        class="bg-green-600 hover:bg-green-700 text-white font-semibold py-3 rounded-xl transition">
                    <i class="fas fa-sync-alt ml-2"></i> تجديد 30 يوماً
                </button>
                <button onclick="deleteAgent()" 
                        class="bg-red-600 hover:bg-red-700 text-white font-semibold py-3 rounded-xl transition">
                    <i class="fas fa-trash ml-2"></i> حذف الوكيل
                </button>
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
            <input type="text" id="new_name" class="w-full p-3 border rounded-xl" placeholder="الاسم الجديد">
            <div class="flex gap-3 mt-6">
                <button onclick="hideEditNameModal()" class="flex-1 py-3 border rounded-xl">إلغاء</button>
                <button onclick="updateName()" class="flex-1 py-3 bg-blue-600 text-white rounded-xl">تعديل</button>
            </div>
        </div>
    </div>

    <!-- Modal تعديل كلمة المرور -->
    <div id="editPasswordModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl w-full max-w-md p-6">
            <h2 class="text-2xl font-bold mb-5">تعديل كلمة المرور</h2>
            <input type="text" id="new_password" class="w-full p-3 border rounded-xl" placeholder="كلمة المرور الجديدة">
            <div class="flex gap-3 mt-6">
                <button onclick="hideEditPasswordModal()" class="flex-1 py-3 border rounded-xl">إلغاء</button>
                <button onclick="updatePassword()" class="flex-1 py-3 bg-blue-600 text-white rounded-xl">تعديل</button>
            </div>
        </div>
    </div>

    <script>
        const pathParts = window.location.pathname.split('/');
        const agentId = pathParts[pathParts.length - 1];
        
        let agents = JSON.parse(localStorage.getItem('agents')) || [];
        let currentAgent = null;
        
        if (agents.length > 0 && agentId && !isNaN(parseInt(agentId))) {
            currentAgent = agents[parseInt(agentId)];
        }
        
        if (!currentAgent) {
            window.location.href = '/admin';
        }
        
        function formatDate(dateString) {
            try {
                return new Date(dateString).toLocaleDateString('ar-EG');
            } catch(e) {
                return dateString;
            }
        }
        
        function calculateDaysRemaining(endDate) {
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            const end = new Date(endDate);
            end.setHours(0, 0, 0, 0);
            return Math.ceil((end - today) / (1000 * 60 * 60 * 24));
        }
        
        function loadAgentDetails() {
            document.getElementById('agentName').innerText = currentAgent.name;
            document.getElementById('detailsName').innerText = currentAgent.name;
            document.getElementById('detailsUsername').innerText = currentAgent.username;
            document.getElementById('detailsPassword').innerText = currentAgent.password;
            document.getElementById('detailsStartDate').innerText = formatDate(currentAgent.start_date);
            document.getElementById('detailsEndDate').innerText = formatDate(currentAgent.end_date);
            
            const daysLeft = calculateDaysRemaining(currentAgent.end_date);
            const daysLeftSpan = document.getElementById('detailsDaysLeft');
            
            if (daysLeft < 0) {
                daysLeftSpan.innerHTML = '<span class="text-red-600">منتهي</span>';
            } else {
                daysLeftSpan.innerHTML = `<span class="text-green-600">${daysLeft} يوم متبقي</span>`;
            }
        }
        
        function saveAgent() {
            agents[agentId] = currentAgent;
            localStorage.setItem('agents', JSON.stringify(agents));
        }
        
        function renewAgent() {
            const newEndDate = new Date(currentAgent.end_date);
            newEndDate.setDate(newEndDate.getDate() + 30);
            currentAgent.end_date = newEndDate.toISOString().split('T')[0];
            saveAgent();
            loadAgentDetails();
            alert('تم تجديد اشتراك الوكيل بنجاح!');
        }
        
        function deleteAgent() {
            if (confirm('هل أنت متأكد من حذف هذا الوكيل؟')) {
                agents.splice(agentId, 1);
                localStorage.setItem('agents', JSON.stringify(agents));
                window.location.href = '/admin';
            }
        }
        
        function showEditNameModal() {
            document.getElementById('new_name').value = currentAgent.name;
            document.getElementById('editNameModal').classList.remove('hidden');
        }
        function hideEditNameModal() {
            document.getElementById('editNameModal').classList.add('hidden');
        }
        function updateName() {
            const newName = document.getElementById('new_name').value.trim();
            if (!newName) { alert('الرجاء إدخال الاسم الجديد'); return; }
            currentAgent.name = newName;
            saveAgent();
            loadAgentDetails();
            hideEditNameModal();
            alert('تم تعديل الاسم بنجاح');
        }
        
        function showEditPasswordModal() {
            document.getElementById('new_password').value = '';
            document.getElementById('editPasswordModal').classList.remove('hidden');
        }
        function hideEditPasswordModal() {
            document.getElementById('editPasswordModal').classList.add('hidden');
        }
        function updatePassword() {
            const newPass = document.getElementById('new_password').value.trim();
            if (!newPass) { alert('الرجاء إدخال كلمة المرور الجديدة'); return; }
            currentAgent.password = newPass;
            saveAgent();
            loadAgentDetails();
            hideEditPasswordModal();
            alert('تم تعديل كلمة المرور بنجاح');
        }
        
        loadAgentDetails();
    </script>

</body>
</html>