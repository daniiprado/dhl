<?php

namespace App\Request;

class Session extends Request
{
    public function __construct()
    {
        parent::__construct($_SESSION);
    }

    public function forget($key)
    {
        if ($this->has($key)) {
            unset($_SESSION[$key]);
        }
    }

    public function forgetAll()
    {
        session_unset();
    }
}