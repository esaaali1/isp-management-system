<?php

use Illuminate\Support\Facades\Route;

// صفحة الدخول الموحدة
Route::get('/login', function () {
    return view('login');
});

// صفحات الأدمن
Route::get('/admin', function () {
    return view('admin');
});

// صفحة تفاصيل الوكيل (للمشاهدة من قبل الأدمن)
Route::get('/admin/agent/{id}', function ($id) {
    return view('agent-details');
});

// صفحات الوكيل
Route::get('/agent/dashboard', function () {
    return view('agent-dashboard');
});

Route::get('/agent/clients/all', function () {
    return view('agent-clients-all');
});

Route::get('/agent/clients/expired', function () {
    return view('agent-clients-expired');
});

Route::get('/agent/client/{id}', function ($id) {
    return view('agent-client-details');
});

// الصفحة الرئيسية تذهب إلى الدخول
Route::get('/', function () {
    return redirect('/login');
});