<?php

if ( ! function_exists('env') ) {
    function env($key, $default = null) {
        $value = getenv($key);
        switch (strtolower($value)) {
            case 'true':
            case '(true)':
                return true;
            case 'false':
            case '(false)':
                return false;
            case 'empty':
            case '(empty)':
                return '';
            case 'null':
            case '(null)':
                return null;
            default:
                return $value ?? $default;
        }
    }
}