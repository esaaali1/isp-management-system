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
                    <h1 class="text-2xl font-bold text-gray-800" id="clientName"></h1>
                    <p class="text-gray-500 text-sm mt-1">تفاصيل المشترك وحالة الاشتراك</p>
                </div>
                <a href="/agent/clients/all" class="text-blue-600 hover:text-blue-700">
                    <i class="fas fa-arrow-right text-xl"></i> العودة
                </a>
            </div>
        </div>

        <!-- بطاقة التفاصيل -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="p-6 space-y-5">
                <!-- اسم المشترك (الاسم الكامل) مع زر تعديل -->
                <div class="flex justify-between items-center pb-3 border-b">
                    <span class="text-gray-600 font-medium"><i class="fas fa-user-circle ml-2 w-6"></i>اسم المشترك</span>
                    <div class="flex items-center gap-2">
                        <span class="text-gray-800 font-semibold" id="detailsFullname"></span>
                        <button onclick="showEditFullnameModal()" class="text-blue-500 hover:text-blue-700 text-sm">
                            <i class="fas fa-edit"></i> تعديل
                        </button>
                    </div>
                </div>

                <!-- اسم المستخدم (PPPoE) -->
                <div class="flex justify-between items-center pb-3 border-b">
                    <span class="text-gray-600 font-medium"><i class="fas fa-user ml-2 w-6"></i>اسم المستخدم</span>
                    <span class="text-gray-800 font-semibold" id="detailsUsername"></span>
                </div>
                
                <!-- كلمة المرور مع زر تعديل -->
                <div class="flex justify-between items-center pb-3 border-b">
                    <span class="text-gray-600 font-medium"><i class="fas fa-lock ml-2 w-6"></i>كلمة المرور</span>
                    <div class="flex items-center gap-2">
                        <span class="text-gray-800 font-mono bg-gray-100 px-3 py-1 rounded" id="detailsPassword"></span>
                        <button onclick="showEditPasswordModal()" class="text-blue-500 hover:text-blue-700 text-sm">
                            <i class="fas fa-edit"></i> تعديل
                        </button>
                    </div>
                </div>
                
                <!-- نوع الباقة -->
                <div class="flex justify-between items-center pb-3 border-b">
                    <span class="text-gray-600 font-medium"><i class="fas fa-tag ml-2 w-6"></i>نوع الباقة</span>
                    <span class="px-3 py-1 rounded-full text-sm font-semibold" id="detailsPackage"></span>
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
            
            <!-- الأزرار -->
            <div class="p-6 bg-gray-50 flex flex-wrap gap-3">
                <button onclick="renewSubscription()" 
                        class="flex-1 bg-green-600 hover:bg-green-700 text-white font-semibold py-3 rounded-xl transition">
                    <i class="fas fa-sync-alt ml-2"></i> تجديد 30 يوماً
                </button>
                <button onclick="showEditDateModal()" 
                        class="flex-1 bg-yellow-600 hover:bg-yellow-700 text-white font-semibold py-3 rounded-xl transition">
                    <i class="fas fa-calendar-edit ml-2"></i> تعديل التاريخ
                </button>
                <button onclick="showChangePackageModal()" 
                        class="flex-1 bg-purple-600 hover:bg-purple-700 text-white font-semibold py-3 rounded-xl transition">
                    <i class="fas fa-exchange-alt ml-2"></i> تغيير الباقة
                </button>
                <button onclick="deleteClient()" 
                        class="flex-1 bg-red-600 hover:bg-red-700 text-white font-semibold py-3 rounded-xl transition">
                    <i class="fas fa-trash ml-2"></i> حذف المشترك
                </button>
            </div>
        </div>
    </div>

    <!-- Modal تعديل الاسم -->
    <div id="editFullnameModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl w-full max-w-md p-6">
            <h2 class="text-2xl font-bold mb-5">تعديل اسم المشترك</h2>
            <input type="text" id="new_fullname" class="w-full p-3 border rounded-xl" placeholder="الاسم الجديد">
            <div class="flex gap-3 mt-6">
                <button onclick="hideEditFullnameModal()" class="flex-1 py-3 border rounded-xl">إلغاء</button>
                <button onclick="updateFullname()" class="flex-1 py-3 bg-blue-600 text-white rounded-xl">تعديل</button>
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

    <!-- Modal تعديل التاريخ -->
    <div id="editDateModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl w-full max-w-md p-6">
            <h2 class="text-2xl font-bold mb-5">تعديل تاريخ الانتهاء</h2>
            <input type="date" id="new_end_date" class="w-full p-3 border rounded-xl">
            <div class="flex gap-3 mt-6">
                <button onclick="hideEditDateModal()" class="flex-1 py-3 border rounded-xl">إلغاء</button>
                <button onclick="updateEndDate()" class="flex-1 py-3 bg-blue-600 text-white rounded-xl">تعديل</button>
            </div>
        </div>
    </div>

    <!-- Modal تغيير الباقة -->
    <div id="changePackageModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl w-full max-w-md p-6">
            <h2 class="text-2xl font-bold mb-5">تغيير الباقة</h2>
            <div class="grid grid-cols-3 gap-3">
                <button onclick="changePackage('Economy')" class="py-3 border rounded-xl text-center">Economy</button>
                <button onclick="changePackage('Standard')" class="py-3 border rounded-xl text-center">Standard</button>
                <button onclick="changePackage('Business')" class="py-3 border rounded-xl text-center">Business</button>
            </div>
            <button onclick="hideChangePackageModal()" class="w-full mt-6 py-3 border rounded-xl">إلغاء</button>
        </div>
    </div>

    <script>
        const clientId = window.location.pathname.split('/')[3];
        let currentAgent = JSON.parse(sessionStorage.getItem('currentAgent'));
        let clients = JSON.parse(localStorage.getItem(`clients_${currentAgent.username}`)) || [];
        let currentClient = clients[clientId];

        if (!currentClient) window.location.href = '/agent/clients/all';

        const packageColors = {
            'Economy': 'bg-green-100 text-green-600',
            'Standard': 'bg-blue-100 text-blue-600',
            'Business': 'bg-purple-100 text-purple-600'
        };

        function formatDate(dateString) {
            try { return new Date(dateString).toLocaleDateString('ar-EG'); }
            catch(e) { return dateString; }
        }

        function calculateDaysRemaining(endDate) {
            const today = new Date(); today.setHours(0,0,0,0);
            const end = new Date(endDate); end.setHours(0,0,0,0);
            return Math.ceil((end - today) / (1000*60*60*24));
        }

        function loadDetails() {
            document.getElementById('clientName').innerText = currentClient.fullname || currentClient.username;
            document.getElementById('detailsFullname').innerText = currentClient.fullname || currentClient.username;
            document.getElementById('detailsUsername').innerText = currentClient.username;
            document.getElementById('detailsPassword').innerText = currentClient.password;
            document.getElementById('detailsPackage').innerHTML = `<span class="${packageColors[currentClient.package]} px-3 py-1 rounded-full">${currentClient.package}</span>`;
            document.getElementById('detailsStartDate').innerText = formatDate(currentClient.start_date);
            document.getElementById('detailsEndDate').innerText = formatDate(currentClient.end_date);
            const daysLeft = calculateDaysRemaining(currentClient.end_date);
            document.getElementById('detailsDaysLeft').innerHTML = daysLeft < 0 ? '<span class="text-red-600">منتهي</span>' : `<span class="text-green-600">${daysLeft} يوم متبقي</span>`;
        }

        function saveClient() {
            clients[clientId] = currentClient;
            localStorage.setItem(`clients_${currentAgent.username}`, JSON.stringify(clients));
        }

        // تعديل الاسم
        function showEditFullnameModal() {
            document.getElementById('new_fullname').value = currentClient.fullname || '';
            document.getElementById('editFullnameModal').classList.remove('hidden');
        }
        function hideEditFullnameModal() { document.getElementById('editFullnameModal').classList.add('hidden'); }
        function updateFullname() {
            const newName = document.getElementById('new_fullname').value.trim();
            if (!newName) { alert('الرجاء إدخال الاسم'); return; }
            currentClient.fullname = newName;
            saveClient();
            loadDetails();
            hideEditFullnameModal();
            alert('تم تعديل الاسم بنجاح');
        }

        // تعديل كلمة المرور
        function showEditPasswordModal() {
            document.getElementById('new_password').value = '';
            document.getElementById('editPasswordModal').classList.remove('hidden');
        }
        function hideEditPasswordModal() { document.getElementById('editPasswordModal').classList.add('hidden'); }
        function updatePassword() {
            const newPass = document.getElementById('new_password').value.trim();
            if (!newPass) { alert('الرجاء إدخال كلمة المرور الجديدة'); return; }
            currentClient.password = newPass;
            saveClient();
            loadDetails();
            hideEditPasswordModal();
            alert('تم تعديل كلمة المرور بنجاح');
        }

        // تجديد 30 يوماً
        function renewSubscription() {
            const newEnd = new Date(currentClient.end_date);
            newEnd.setDate(newEnd.getDate() + 30);
            currentClient.end_date = newEnd.toISOString().split('T')[0];
            saveClient();
            loadDetails();
            alert('تم تجديد الاشتراك 30 يوماً');
        }

        // تعديل التاريخ
        function showEditDateModal() {
            document.getElementById('new_end_date').value = currentClient.end_date;
            document.getElementById('editDateModal').classList.remove('hidden');
        }
        function hideEditDateModal() { document.getElementById('editDateModal').classList.add('hidden'); }
        function updateEndDate() {
            const newDate = document.getElementById('new_end_date').value;
            if (newDate < new Date().toISOString().split('T')[0]) { alert('لا يمكن اختيار تاريخ مضى'); return; }
            currentClient.end_date = newDate;
            saveClient();
            loadDetails();
            hideEditDateModal();
            alert('تم تعديل تاريخ الانتهاء');
        }

        // تغيير الباقة
        function showChangePackageModal() { document.getElementById('changePackageModal').classList.remove('hidden'); }
        function hideChangePackageModal() { document.getElementById('changePackageModal').classList.add('hidden'); }
        function changePackage(pkg) {
            currentClient.package = pkg;
            saveClient();
            loadDetails();
            hideChangePackageModal();
            alert(`تم تغيير الباقة إلى ${pkg}`);
        }

        // حذف المشترك
        function deleteClient() {
            if (confirm('هل أنت متأكد من حذف هذا المشترك؟')) {
                clients.splice(clientId, 1);
                localStorage.setItem(`clients_${currentAgent.username}`, JSON.stringify(clients));
                window.location.href = '/agent/clients/all';
            }
        }

        loadDetails();
    </script>

</body>
</html>