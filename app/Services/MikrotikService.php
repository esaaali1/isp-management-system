<?php

namespace App\Services;

class MikrotikService
{
    protected $socket;

    // ===================== دوال الاتصال الأساسية =====================

    public function connect($host, $user, $pass, $port = 8728)
    {
        $this->socket = @fsockopen($host, $port, $errno, $errstr, 10);
        if (!$this->socket) {
            throw new \Exception("فشل الاتصال بالمايكروتيك: $errstr ($errno)");
        }

        $this->sendSentence(['/login', "=name=$user", "=password=$pass"]);
        $reply = $this->readReply();

        if (!in_array('!done', $reply)) {
            throw new \Exception("فشل تسجيل الدخول إلى المايكروتيك");
        }
    }

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

    private function sendSentence($words)
    {
        foreach ($words as $word) {
            $this->sendWord($word);
        }
        $this->sendWord('');
    }

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

    public function createPppoeUser($username, $password, $profile, $agent)
    {
        $this->connect(
            $agent->mikrotik_host,
            $agent->mikrotik_user,
            $agent->mikrotik_pass,
            $agent->mikrotik_port
        );

        return $this->execCommand('/ppp/secret/add', [
            'name' => $username,
            'password' => $password,
            'service' => 'pppoe',
            'profile' => $profile,
        ]);
    }

    public function disablePppoeUser($username, $agent)
    {
        $this->connect(
            $agent->mikrotik_host,
            $agent->mikrotik_user,
            $agent->mikrotik_pass,
            $agent->mikrotik_port
        );

        return $this->execCommand('/ppp/secret/set', [
            '.id' => $username,
            'disabled' => 'yes',
        ]);
    }

    public function enablePppoeUser($username, $agent)
    {
        $this->connect(
            $agent->mikrotik_host,
            $agent->mikrotik_user,
            $agent->mikrotik_pass,
            $agent->mikrotik_port
        );

        return $this->execCommand('/ppp/secret/set', [
            '.id' => $username,
            'disabled' => 'no',
        ]);
    }

    public function deletePppoeUser($username, $agent)
    {
        $this->connect(
            $agent->mikrotik_host,
            $agent->mikrotik_user,
            $agent->mikrotik_pass,
            $agent->mikrotik_port
        );

        return $this->execCommand('/ppp/secret/remove', [
            '.id' => $username,
        ]);
    }

    public function changePppoePassword($username, $newPassword, $agent)
    {
        $this->connect(
            $agent->mikrotik_host,
            $agent->mikrotik_user,
            $agent->mikrotik_pass,
            $agent->mikrotik_port
        );

        return $this->execCommand('/ppp/secret/set', [
            '.id' => $username,
            'password' => $newPassword,
        ]);
    }

    public function changePppoeProfile($username, $profile, $agent)
    {
        $this->connect(
            $agent->mikrotik_host,
            $agent->mikrotik_user,
            $agent->mikrotik_pass,
            $agent->mikrotik_port
        );

        return $this->execCommand('/ppp/secret/set', [
            '.id' => $username,
            'profile' => $profile,
        ]);
    }

    public function getActiveUsers($agent)
    {
        $this->connect(
            $agent->mikrotik_host,
            $agent->mikrotik_user,
            $agent->mikrotik_pass,
            $agent->mikrotik_port
        );

        return $this->execCommand('/ppp/active/print');
    }

    /**
     * الحصول على عنوان IP الخاص بمشترك معين (إذا كان متصلاً)
     */
    public function getClientIP($username, $agent)
    {
        // الاتصال بالمايكروتيك الخاص بالوكيل
        $this->connect(
            $agent->mikrotik_host,
            $agent->mikrotik_user,
            $agent->mikrotik_pass,
            $agent->mikrotik_port
        );

        // جلب قائمة المتصلين
        $response = $this->execCommand('/ppp/active/print');

        // البحث عن المستخدم في القائمة
        $ip = null;
        $currentUser = null;

        foreach ($response as $line) {
            if (strpos($line, '=name=') === 0) {
                $currentUser = substr($line, 6);
            }
            if ($currentUser === $username && strpos($line, '=address=') === 0) {
                $ip = substr($line, 9);
                break;
            }
        }

        return $ip; // إذا كان null فهذا يعني أنه غير متصل
    }
}