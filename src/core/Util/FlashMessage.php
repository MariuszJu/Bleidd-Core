<?php

namespace Bleidd\Util;

use Bleidd\Application\Runtime;

class FlashMessage
{

    const SUCCESS = 'success';
    const ERROR = 'danger';
    const DANGER = 'danger';
    const INFO = 'info';
    const WARN = 'warning';

    /**
     * Add message
     *
     * @param string $type
     * @param string $message
     * @param bool   $checkIfAlreadyExists
     * @return bool
     */
    public function addMessage(string $type, string $message, bool $checkIfAlreadyExists = false)
    {
        if (!in_array($type, [self::SUCCESS, self::ERROR, self::INFO, self::WARN])) {
            return false;
        }

        if ($checkIfAlreadyExists) {
            $messages = Runtime::session()->get('messanger_messages')[$type] ?? [];
            if (is_array($messages) && in_array($message, $messages)) {
                return false;
            }
        }

        $messages[$type][] = $message;
        Runtime::session()->set('messanger_messages', $messages);

        return true;
    }

    /**
     * @param string $message
     * @param bool   $checkIfAlreadyExists
     */
    public function addSuccessMessage(string $message, bool $checkIfAlreadyExists = true)
    {
        self::addMessage(self::SUCCESS, $message, $checkIfAlreadyExists);
    }

    /**
     * @param string $message
     * @param bool   $checkIfAlreadyExists
     */
    public function addErrorMessage(string $message, bool $checkIfAlreadyExists = true)
    {
        self::addMessage(self::ERROR, $message, $checkIfAlreadyExists);
    }

    /**
     * @param string $message
     * @param bool   $checkIfAlreadyExists
     */
    public function addInfoMessage(string $message, bool $checkIfAlreadyExists = true)
    {
        self::addMessage(self::INFO, $message, $checkIfAlreadyExists);
    }

    /**
     * @param string $message
     * @param bool   $checkIfAlreadyExists
     */
    public function addWarningMessage(string $message, bool $checkIfAlreadyExists = true)
    {
        self::addMessage(self::WARN, $message, $checkIfAlreadyExists);
    }

    /**
     * Get messages
     *
     * @param string|null $type
     * @param bool        $clear
     * @return array|false
     */
    public function getMessages(string $type = null, bool $clear = true)
    {
        if (empty($type)) {
            $messages = Runtime::session()->get('messanger_messages') ?? [];

            if ($clear) {
                Runtime::session()->unset('messanger_messages');
            }

            return is_array($messages) ? $messages : [];
        }

        if (!in_array($type, [self::SUCCESS, self::ERROR, self::INFO, self::WARN])) {
            return false;
        }

        $messages = Runtime::session()->get('messanger_messages')[$type] ?? [];

        if ($clear) {
            unset($messages[$type]);
            Runtime::session()->set('messanger_messages', $messages);
        }

        return is_array($messages) ? $messages : [];
    }
    
}
