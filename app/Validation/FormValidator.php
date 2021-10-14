<?php

namespace App\Validation;


trait FormValidator
{
    public function validate()
    {
        $validator = new Validator();
        $keys = array_keys($this->rules());
        foreach ($keys as $key) {
            $validations = explode('|', $this->rules()[$key] ?? []);
            foreach ($validations as $validation) {
                $executor = explode(':', $validation, 2);
                $function = $executor[0] ?? 'invalid';
                $params = $executor[1] ?? null;
                if (method_exists($validator, $function)) {
                    try {
                        $method = new \ReflectionMethod($validator, $function);
                        if (count($method->getParameters()) > 1) {
                            $passes = $validator->{$function}(
                                $this->request->get($key),
                                $this->checkRule($function, $params)
                            );
                        } else {
                            $passes = $validator->{$function}(
                                $this->request->get($key)
                            );
                        }
                        if (! $passes) {
                            $this->errors[] = $this->messages($key, $params)[$function] ?? "Error function $function - field: $key - params: $params";
                        }
                    } catch (\ReflectionException $e) {
                        $this->errors[] = 'Validation or rule does not exist';
                    }
                } else {
                    $this->errors[] = "Validation '$function' does not exist";
                }
            }
        }
    }

    /**
     * @param $attribute
     * @param null $additional
     * @return string[]
     */
    public function messages($attribute, $additional = null)
    {
        return [
            'boolean'   => "The $attribute field must be true or false.",
            'email' => "The $attribute field must be a valid email address.",
            'numeric'   => "The $attribute field must be a number.",
            'float' => "The $attribute field must be a float.",
            'ip'    => "The $attribute field is not a valid IP.",
            'url'   => "The $attribute field debe ser una URL válida",
            'date'  => "The $attribute field is not a valid date.",
            'date_format'   => "The $attribute field does not match the format $additional",
            'in'    => "The $attribute field must be in $additional.",
            'not_in'    => "The $attribute field must not be in $additional.",
            'array' => "The $attribute field must be an array.",
            'required'  => "The $attribute field is required.",
        ];
    }

    /**
     * @param $name
     * @param $params
     * @return false|mixed|string[]|void
     */
    public function checkRule($name, $params)
    {
        switch ($name) {
            case 'not_in':
            case 'in':
                return explode(',', $params);
            case 'date_format':
                return $params;
        }
    }
}