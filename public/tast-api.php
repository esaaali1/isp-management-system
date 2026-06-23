<?php

$host = '192.168.88.1';
$port = 8728;
$user = 'admin';
$pass = '1998';

// فتح اتصال TCP
$socket = fsockopen($host, $port, $errno, $errstr, 30);
if (!$socket) {
    die("فشل الاتصال: $errstr ($errno)");
}

echo "✅ تم الاتصال بالمايكروتيك<br>";

// دالة لإرسال كلمة (Word)
function sendWord($socket, $word) {
    $len = strlen($word);
    if ($len < 0x80) {
        fwrite($socket, chr($len));
    } elseif ($len < 0x4000) {
        fwrite($socket, chr(($len >> 8) | 0x80));
        fwrite($socket, chr($len & 0xFF));
    } else {
        fwrite($socket, chr(0x80));
        fwrite($socket, chr(($len >> 16) & 0xFF));
        fwrite($socket, chr(($len >> 8) & 0xFF));
        fwrite($socket, chr($len & 0xFF));
    }
    fwrite($socket, $word);
}

// دالة لقراءة كلمة
function readWord($socket) {
    $c = fread($socket, 1);
    if ($c === false) return false;
    $c = ord($c);
    if ($c < 0x80) {
        $len = $c;
    } elseif ($c < 0xC0) {
        $c2 = fread($socket, 1);
        $len = (($c & 0x3F) << 8) + ord($c2);
    } else {
        fread($socket, 1);
        $c2 = fread($socket, 1);
        $c3 = fread($socket, 1);
        $len = (($c & 0x3F) << 24) + (ord($c2) << 16) + (ord($c3) << 8) + ord($c4);
    }
    return fread($socket, $len);
}

// دالة لإرسال جملة كاملة
function sendSentence($socket, $words) {
    foreach ($words as $word) {
        sendWord($socket, $word);
    }
    sendWord($socket, ''); // zero-length word
}

// دالة لقراءة الرد
function readReply($socket) {
    $reply = [];
    while (true) {
        $word = readWord($socket);
        if ($word === false) break;
        if ($word === '') continue;
        $reply[] = $word;
        if ($word === '!done') break;
        if ($word === '!trap') break;
    }
    return $reply;
}

// 1. إرسال أمر تسجيل الدخول
echo "🔐 محاولة تسجيل الدخول...<br>";
sendSentence($socket, ['/login', "=name=$user", "=password=$pass"]);
$reply = readReply($socket);
echo "الرد: " . implode(' | ', $reply) . "<br>";

// التحقق من النجاح
if (in_array('!done', $reply)) {
    echo "✅ تم تسجيل الدخول بنجاح!<br>";

    // 2. إرسال أمر لعرض معلومات النظام
    echo "📊 جلب معلومات النظام...<br>";
    sendSentence($socket, ['/system/resource/print']);
    $reply = readReply($socket);
    echo "الرد: <pre>";
    print_r($reply);
    echo "</pre>";
} else {
    echo "❌ فشل تسجيل الدخول. تحقق من اسم المستخدم وكلمة المرور.<br>";
}

fclose($socket);
?>