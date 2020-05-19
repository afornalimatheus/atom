<?php

namespace App\Messages;

use Symfony\Component\HttpFoundation\Session\Session;

class FlashMessenger
{
    const FLASH_MESSAGES = 'flash_messages';

    const SUCCESS = 3;
    const ALERT = 2;
    const WARING = 1;

    private $session;

    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    public function add($messageSend, $type = self::SUCCESS)
    {
        if (!$this->session->has(self::FLASH_MESSAGES)) {
            $this->session->set(self::FLASH_MESSAGES, []);
        }

        $messages = $this->session->get(self::FLASH_MESSAGES);

        if (isset($messageSend)) {
            $messages = array_merge($messages, $messageSend);
            $this->session->set(self::FLASH_MESSAGES, $messages);
        }

    }

    public function get($clear = true)
    {
        $messages = [];
        if ($this->session->has(self::FLASH_MESSAGES)) {
            $messages = $this->session->get(self::FLASH_MESSAGES);
            if ($clear) {
                $this->session->remove(self::FLASH_MESSAGES);
            }
        }
        return $messages;
    }
}