<?php

namespace App\Services;

class MikrotikService
{
    protected $socket;
    protected $host;
    protected $port;
    protected $user;
    protected $pass;

    public function __construct()
    {
        $this->host = config('mikrotik.host');
        $this->port = config('mikrotik.port');
        $this->user = config('mikrotik.user');
        $this->pass = config('mikrotik.pass');
        $this->connect();
    }

    /**
     * فتح الاتصال بالمايكروتيك
     */
    private function connect()
    {
        $this->socket = @fsockopen($this->host, $this->port, $errno, $errstr, 10);
        if (!$this->socket) {
            throw new \Exception("فشل الاتصال بالمايكروتيك: $errstr ($errno)");
        }

        // تسجيل الدخول
        $this->sendSentence(['/login', "=name={$this->user}", "=password={$this->pass}"]);
        $reply = $this->readReply();

        if (!in_array('!done', $reply)) {
            throw new \Exception("فشل تسجيل الدخول: " . implode(' | ', $reply));
        }
    }

    /**
     * إرسال كلمة واحدة
     */
    private function sendWord($word)
    {
        $len = strlen($word);
        if ($len < 0x80) {
            fwrite($this->socket, chr($len));
        } elseif ($len < 0x4000) {
            fwrite($this->socket, chr(($len >> 8) | 0x80));
            fwrite($this->socket, chr($len & 0xFF));
        } else {
            fwrite($this->socket, chr(0x80));
            fwrite($this->socket, chr(($len >> 16) & 0xFF));
            fwrite($this->socket, chr(($len >> 8) & 0xFF));
            fwrite($this->socket, chr($len & 0xFF));
        }
        fwrite($this->socket, $word);
    }

    /**
     * قراءة كلمة واحدة
     */
    private function readWord()
    {
        $c = fread($this->socket, 1);
        if ($c === false || strlen($c) === 0) {
            return false;
        }
        $c = ord($c);
        if ($c < 0x80) {
            $len = $c;
        } elseif ($c < 0xC0) {
            $c2 = fread($this->socket, 1);
            if ($c2 === false || strlen($c2) === 0) return false;
            $len = (($c & 0x3F) << 8) + ord($c2);
        } else {
            fread($this->socket, 1);
            $c2 = fread($this->socket, 1);
            $c3 = fread($this->socket, 1);
            $c4 = fread($this->socket, 1);
            if ($c2 === false || $c3 === false || $c4 === false) return false;
            $len = (($c & 0x3F) << 24) + (ord($c2) << 16) + (ord($c3) << 8) + ord($c4);
        }

        if ($len == 0) {
            return '';
        }
        $data = fread($this->socket, $len);
        return ($data === false) ? false : $data;
    }

    /**
     * إرسال جملة كاملة
     */
    private function sendSentence($words)
    {
        foreach ($words as $word) {
            $this->sendWord($word);
        }
        $this->sendWord('');
    }

    /**
     * قراءة الرد بالكامل
     */
    private function readReply()
    {
        $reply = [];
        while (true) {
            $word = $this->readWord();
            if ($word === false) {
                $reply[] = '!fatal (connection lost)';
                break;
            }
            if ($word === '') {
                continue;
            }
            $reply[] = $word;
            if ($word === '!done' || $word === '!trap') {
                break;
            }
        }
        return $reply;
    }

    /**
     * تنفيذ أمر عام
     */
    private function execCommand($command, $params = [])
    {
        $words = [$command];
        foreach ($params as $key => $value) {
            $words[] = "=$key=$value";
        }
        $this->sendSentence($words);
        return $this->readReply();
    }

    // ===================== الوظائف العامة =====================

    public function testConnection()
    {
        try {
            $reply = $this->execCommand('/system/resource/print');
            return ['success' => true, 'data' => $reply];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * إنشاء مستخدم PPPoE جديد (يستخدم profile=default)
     */
    public function createPppoeUser($username, $password, $profile = 'default')
    {
        return $this->execCommand('/ppp/secret/add', [
            'name' => $username,
            'password' => $password,
            'service' => 'pppoe',
            'profile' => $profile,
        ]);
    }

    public function disablePppoeUser($username)
    {
        return $this->execCommand('/ppp/secret/set', [
            '.id' => $username,
            'disabled' => 'yes',
        ]);
    }

    public function enablePppoeUser($username)
    {
        return $this->execCommand('/ppp/secret/set', [
            '.id' => $username,
            'disabled' => 'no',
        ]);
    }

    public function deletePppoeUser($username)
    {
        return $this->execCommand('/ppp/secret/remove', [
            '.id' => $username,
        ]);
    }

    public function changePppoePassword($username, $newPassword)
    {
        return $this->execCommand('/ppp/secret/set', [
            '.id' => $username,
            'password' => $newPassword,
        ]);
    }

    public function changePppoeProfile($username, $profile)
    {
        return $this->execCommand('/ppp/secret/set', [
            '.id' => $username,
            'profile' => $profile,
        ]);
    }

    public function getActiveUsers()
    {
        return $this->execCommand('/ppp/active/print');
    }
}