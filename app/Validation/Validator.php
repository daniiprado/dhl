<?php

namespace App\Validation;

use Countable;
use DateTimeInterface;

class Validator
{
    public function boolean($value)
    {
        $acceptable = [true, false, 0, 1, '0', '1', 'true', 'false'];
        return in_array($value, $acceptable, true);
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

    public function after($value)
    {
        if (! is_string($value) && ! is_numeric($value) && ! $value instanceof DateTimeInterface) {
            return false;
        }
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

    public function string($value)
    {
        return is_string($value);
    }

    public function size($value, int $size)
    {
        if (is_numeric($value)) {
            return $value === $size;
        } elseif (is_array($value)) {
            return count($value) === $size;
        }
        return mb_strlen(trim((string) $value)) === $size;
    }

    public function max($value, int $size)
    {
        if (is_numeric($value)) {
            return $value <= $size;
        } elseif (is_array($value)) {
            return count($value) <= $size;
        }
        return mb_strlen(trim((string) $value)) <= $size;
    }

    public function min($value, int $size)
    {
        if (is_numeric($value)) {
            return $value >= $size;
        } elseif (is_array($value)) {
            return count($value) >= $size;
        }
        return mb_strlen(trim((string) $value)) >= $size;
    }

    public function required($value = null)
    {
        if (is_null($value)) {
            return false;
        } elseif (is_string($value) && trim($value) === '') {
            return false;
        } elseif ((is_array($value) || $value instanceof Countable) && count($value) < 1) {
            return false;
        }
        return true;
    }
}