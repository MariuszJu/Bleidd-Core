<?php

namespace Bleidd\Session;

use Bleidd\Application\Runtime;
use Bleidd\Application\Application;

final class Session
{

    const TTL_5S = 5;
    const TTL_1M = 60;
    const TTL_5M = 300;
    const TTL_15M = 900;
    const TTL_1H = 3600;
    const TTL_6H = 21600;
    const TTL_1D = 86400;
    const TTL_7D = 604800;
    const TTL_1MO = 2592000;

    /** @var string|null */
    private $session;

    /** @var string */
    private $encryptionKey;

    /** @var string */
    private $sessionKey = 'session_data_hash';

    private function __clone() {}
    private function __wakeup() {}

    /**
     * Session constructor
     *
     * @param Application $application
     */
    public function __construct(Application $application)
    {
        $this->init();
    }

    /**
     * @return self
     */
    private function init(): self
    {
        session_start();

        $this->encryptionKey = Runtime::config()->configKey('session_encryption_key');
        $this->session = $_SESSION[$this->sessionKey] ?? null;

        return $this;
    }

    /**
     * @param array $sessionData
     * @return string
     */
    private function encryptSession(array $sessionData): string
    {
        $plainText = serialize($sessionData);
        $ivlen = openssl_cipher_iv_length($cipher = 'AES-128-CBC');
        $iv = openssl_random_pseudo_bytes($ivlen);
        $cipherTextRaw = openssl_encrypt($plainText, $cipher, $this->encryptionKey, $options = OPENSSL_RAW_DATA, $iv);
        $hmac = hash_hmac('sha256', $cipherTextRaw, $this->encryptionKey, true);

        return base64_encode($iv.$hmac.$cipherTextRaw);
    }

    /**
     * @return array
     */
    private function decryptSession(): array
    {
        $c = base64_decode($this->session);
        $ivlen = openssl_cipher_iv_length($cipher = 'AES-128-CBC');
        $iv = substr($c, 0, $ivlen);
        $hmac = substr($c, $ivlen, $sha2len = 32);
        $cipherTextRaw = substr($c, $ivlen + $sha2len);
        $originalPlainText = openssl_decrypt($cipherTextRaw, $cipher, $this->encryptionKey, $options = OPENSSL_RAW_DATA, $iv);
        $calcmac = hash_hmac('sha256', $cipherTextRaw, $this->encryptionKey, true);

        if (hash_equals($hmac, $calcmac)) {
            $sessionData = @unserialize($originalPlainText);

            if (!is_array($sessionData)) {
                $this->session = null;
                return [];
            }

            return $sessionData;
        }

        $this->session = null;
        return [];
    }

    /**
     * @return array
     */
    private function sessionData(): array
    {
        return !empty($this->session) ? $this->decryptSession() : [];
    }

    /**
     * @param string   $key
     * @param mixed    $value
     * @param int|null $ttl
     * @return self
     */
    public function set(string $key, $value, int $ttl = null): self
    {
        $sessionData = $this->sessionData();
        
        $sessionData[$key] = [
            'value'       => $value,
            'valid_until' => empty($ttl) ? null : (new \DateTime())
                ->add(new \DateInterval(sprintf('PT%sS', $ttl)))
                ->format('Y-m-d H:i:s'),
        ];

        $this->session = $this->encryptSession($sessionData);
        $_SESSION[$this->sessionKey] = $this->session;

        return $this;
    }

    /**
     * @param string $key
     * @param null   $default
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        $sessionData = $this->sessionData();
        
        if (!isset($sessionData[$key])) {
            return null;
        }

        $item = $sessionData[$key];

        if (!isset($item['valid_until']) || empty($item['valid_until'])) {
            return $item['value'];
        }

        $now = new \DateTime();
        $validUntil = new \DateTime($item['valid_until']);

        if ($now->getTimestamp() > $validUntil->getTimestamp()) {
            $this->unset($key);
            return null;
        }

        return $item['value'];
    }

    /**
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        return !empty($this->get($key));
    }

    /**
     * @param string $key
     * @return self
     */
    public function unset(string $key): self
    {
        $sessionData = $this->sessionData();

        if (isset($sessionData[$key])) {
            unset($sessionData[$key]);
        }

        $this->session = $this->encryptSession($sessionData);
        $_SESSION[$this->sessionKey] = $this->session;

        return $this;
    }

    /**
     * @return array
     */
    public function all(): array
    {
        return $this->sessionData();
    }

    /**
     * @return Session
     */
    public function clear(): self
    {
        $this->session = null;

        if (isset($_SESSION[$this->sessionKey])) {
            unset ($_SESSION[$this->sessionKey]);
        }
    }

}
