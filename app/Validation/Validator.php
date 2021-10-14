<?php

namespace App\Validation;

class Validator
{
    public function boolean($value)
    {
        return ! is_null(filter_var($value, FILTER_VALIDATE_BOOLEAN, ['flags' => FILTER_NULL_ON_FAILURE]));
    }

    public function email($value)
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL);
    }

    public function numeric($value)
    {
        return filter_var($value, FILTER_VALIDATE_INT);
    }

    public function float($value)
    {
        return filter_var($value, FILTER_VALIDATE_FLOAT);
    }

    public function ip($value)
    {
        return filter_var($value, FILTER_VALIDATE_IP);
    }

    public function url($value)
    {
        return filter_var($value, FILTER_VALIDATE_URL);
    }

    public function date($value)
    {
        return validate_date($value);
    }

    public function date_format($value, $format)
    {
        return validate_date_format($value, $format);
    }

    public function in($value, array $attributes = [])
    {
        return in_array($value, $attributes);
    }

    public function not_in($value, array $attributes = [])
    {
        return ! $this->in($value, $attributes);
    }

    public function array($value)
    {
        return is_array($value);
    }

    public function required($value = null)
    {
        if (is_bool($value)) {
            return true;
        }

        if (is_array($value)) {
            return count($value) > 0;
        }

        return trim((string) $value) !== '';
    }
}