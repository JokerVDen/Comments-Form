<?php

namespace helpers;

class CsrfHelper
{

    /**
     * Generate new csrf token and store it to session
     * @return string
     */
    static function generateToken()
    {
        $letters_for_token = 'qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM';
        return $_SESSION['csrf_token'] = substr(str_shuffle($letters_for_token), 0, 10);
    }

    /**
     * Get or set csrf token
     *
     * @return string
     */
    static function getOrSetSessionToken()
    {
        if(!isset($_SESSION['csrf_token'])) self::generateToken();
        return $_SESSION['csrf_token'];
    }
}