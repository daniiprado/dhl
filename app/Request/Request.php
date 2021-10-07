<?php

namespace App\Request;

use App\Helpers\Arr;
use stdClass;

class Request
{
    /**
     * @var array
     */
    private $items;

    public function __construct($items = [])
    {
        $this->items = $_GET + $_POST + $items;
    }

    /**
     * @return string
     */
    public function method()
    {
        return strtolower($_SERVER['REQUEST_METHOD']);
    }

    /**
     * @return bool
     */
    public function methodIsGet()
    {
        return $this->method() == 'get';
    }

    /**
     * @return bool
     */
    public function methodIsPost()
    {
        return $this->method() == 'post';
    }

    /**
     * @return bool
     */
    public function methodIsPatch()
    {
        return $this->method() == 'patch';
    }

    /**
     * @return bool
     */
    public function methodIsPut()
    {
        return $this->method() == 'put';
    }

    /**
     * @return bool
     */
    public function methodIsDelete()
    {
        return $this->method() == 'delete';
    }

    /**
     * Returns all params
     *
     * @return array|mixed
     */
    public function all()
    {
        return $this->items;
    }

    /**
     * Returns parameters in Json
     *
     * @return false|string
     */
    function toJson()
    {
        return json_encode($this->items, JSON_PRETTY_PRINT);
    }

    /**
     * Returns a parameter by name.
     *
     * @param string $key     The key
     * @param mixed  $default The default value if the parameter key does not exist
     *
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return array_key_exists($key, $this->all()) ? $this->all()[$key] : $default;
    }

    /**
     * @param $key
     * @param $value
     * @param bool $overwrite
     * @return array|mixed
     */
    public function set($key, $value, bool $overwrite = true)
    {
        data_set($this->items, $key, $value, $overwrite);
        return  $this;
    }

    /**
     * @param $key
     * @param $value
     * @param bool $overwrite
     * @return array|mixed
     */
    public function setsSession($key, $value, bool $overwrite = true)
    {
        data_set($_SESSION, $key, $value, $overwrite);
        return  $this;
    }

    /**
     * Determine if the request contains a given input item key.
     *
     * @param  string|array  $key
     * @return bool
     */
    public function exists($key)
    {
        return $this->has($key);
    }

    /**
     * Determine if the request contains a given input item key.
     *
     * @param  string|array  $key
     * @return bool
     */
    public function has($key)
    {
        $keys = is_array($key) ? $key : func_get_args();

        $input = $this->all();

        foreach ($keys as $value) {
            if (! Arr::has($input, $value)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Determine if the request contains any of the given inputs.
     *
     * @param  string|array  $keys
     * @return bool
     */
    public function hasAny($keys)
    {
        $keys = is_array($keys) ? $keys : func_get_args();

        $input = $this->all();

        foreach ($keys as $key) {
            if (Arr::has($input, $key)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine if the request contains a non-empty value for an input item.
     *
     * @param  string|array  $key
     * @return bool
     */
    public function filled($key)
    {
        $keys = is_array($key) ? $key : func_get_args();

        foreach ($keys as $value) {
            if ($this->isEmptyString($value)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Determine if the request contains a non-empty value for any of the given inputs.
     *
     * @param  string|array  $keys
     * @return bool
     */
    public function anyFilled($keys)
    {
        $keys = is_array($keys) ? $keys : func_get_args();

        foreach ($keys as $key) {
            if ($this->filled($key)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get the keys for all of the input and files.
     *
     * @return array
     */
    public function keys()
    {
        return array_keys($this->input());
    }

    /**
     * Get all of the input except for a specified array of items.
     *
     * @param  array|mixed  $keys
     * @return array
     */
    public function except($keys)
    {
        $keys = is_array($keys) ? $keys : func_get_args();

        $results = $this->all();

        Arr::forget($results, $keys);

        return $results;
    }

    /**
     * Get a subset containing the provided keys with values from the input data.
     *
     * @param  array|mixed  $keys
     * @return array
     */
    public function only($keys)
    {
        $results = [];

        $input = $this->all();

        $placeholder = new stdClass;

        foreach (is_array($keys) ? $keys : func_get_args() as $key) {
            $value = data_get($input, $key, $placeholder);

            if ($value !== $placeholder) {
                Arr::set($results, $key, $value);
            }
        }

        return $results;
    }


    /**
     * Determine if the given input key is an empty string for "has".
     *
     * @param  string  $key
     * @return bool
     */
    protected function isEmptyString($key)
    {
        $value = $this->input($key);

        return ! is_bool($value) && ! is_array($value) && trim((string) $value) === '';
    }

    /**
     * Retrieve an input item from the request.
     *
     * @param  string|null  $key
     * @param  string|array|null  $default
     * @return string|array|null
     */
    public function input(string $key = null, $default = null)
    {
        return data_get($this->all(), $key, $default);
    }
}