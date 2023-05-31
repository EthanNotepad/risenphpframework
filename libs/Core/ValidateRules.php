<?php

namespace libs\Core;

class ValidateRules
{
    private static $alias = [
        'numeric' => ['number', 'num'],
        'symbol' => ['specialcharacter'],
    ];

    public static function getValidationRule($rule)
    {
        $params = [];
        // Check if the rule has any parameters
        if (strpos($rule, ':') !== false) {
            list($rule, $params) = explode(':', $rule, 2);
            $params = explode(',', $params);
        }
        $method = 'validate_' . $rule;
        if (!method_exists(self::class, $method)) {
            // Check if the rule is an alias
            $alias_matched = false;
            foreach (self::$alias as $alias_key => $alias_array) {
                if (in_array($rule, $alias_array)) {
                    $method = 'validate_' . $alias_key;
                    $alias_matched = true;
                    break;
                }
            }
            if (!$alias_matched) {
                return false;
            }
        }
        // Return the method rule and params as an array
        return [
            'method' => [self::class, $method],
            'rule' => $rule, // 'required', 'min', 'max', 'numeric', 'symbol
            'params' => $params,
        ];
    }

    public static function getDefaultMessage($field, $rule, $params)
    {
        $defaultMessages = self::getDefaultMessages();

        if (isset($defaultMessages[$rule])) {
            $defaultMessage = $defaultMessages[$rule];
            $defaultMessage = str_replace(':attribute', $field, $defaultMessage);
            if (!is_null($params)) {
                $defaultMessage = str_replace(':params', implode(', ', $params), $defaultMessage);
            }
            return $defaultMessage;
        }

        return "The $field field is invalid."; // Default fallback message
    }

    private static function getDefaultMessages()
    {
        return [
            'required' => 'the :attribute field is required.',
            'max' => 'the :attribute field can be up to :params characters.',
            'min' => 'the :attribute field must be at least :params characters.',
            'numeric' => 'the :attribute field must be a numeric value.',
            'double' => 'the :attribute field must be a double value.',
            'date' => 'the :attribute field must be a date with the format :params.',
            'time' => 'the :attribute field must be a time with the format HH:MM:SS or HH:MM.',
            'email' => 'the :attribute field must be a valid email address.',
            'phone' => 'the :attribute field must be a valid phone number.',
            'integer' => 'the :attribute field must be an integer.',
            'float' => 'the :attribute field must be a float.',
            'boolean' => 'the :attribute field must be a boolean.',
            'array' => 'the :attribute field must be an array.',
            'alpha' => 'the :attribute field can only contain letters.',
            'alphanumeric' => 'the :attribute field can only contain letters and numbers.',
            'same' => 'the :attribute field must be the same as the :params field.',
            'url' => 'the :attribute field must be a valid URL.',
            'ip' => 'the :attribute field must be a valid IP address.',
            'in' => 'the :attribute field must be one of these values: :params.',
            'not_in' => 'the :attribute field cannot be one of these values: :params.',
            'different' => 'the :attribute field must be different from the :params field.',
            'uppercase' => 'the :attribute field can only contain uppercase letters.',
            'lowercase' => 'the :attribute field can only contain lowercase letters.',
            'symbol' => 'the :attribute field can only contain symbols.',
            'regex' => 'the :attribute field must match the pattern :params.',
        ];
    }

    public static function validate_required($value, $params, $data)
    {
        if (is_numeric($value) || is_bool($value)) {
            return true;
        }
        return !empty($value);
    }

    public static function validate_min($value, $params, $data)
    {
        if (!isset($params[0])) {
            throw new \Exception("Validation rule 'min' requires a parameter.");
        }

        $min = intval($params[0]);

        return strlen($value) >= $min;
    }

    public static function validate_max($value, $params, $data)
    {
        if (!isset($params[0])) {
            throw new \Exception("Validation rule 'max' requires a parameter.");
        }

        $max = intval($params[0]);

        return strlen($value) <= $max;
    }

    public static function validate_numeric($value, $params, $data)
    {
        return is_numeric($value);
    }

    public static function validate_double($value, $params, $data)
    {
        return is_double($value);
    }

    public static function validate_date($value, $params, $data)
    {
        if (!isset($params[0])) {
            throw new \Exception("Validation rule 'date' requires a parameter.");
        }

        $format = $params[0];
        $date = \DateTime::createFromFormat($format, $value);

        return $date && $date->format($format) == $value;
    }

    public static function validate_time($value, $params, $data)
    {
        // support 000:00:00.000000 and 000:00
        $pattern = '/^(-?\d{1,3}):([0-5]\d{1,2})(:[0-5]\d{1,2})?(\.\d{1,6})?$/';

        return preg_match($pattern, $value);
    }

    public static function validate_email($value, $params, $data)
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }

    public static function validate_phone($value, $params, $data)
    {
        return preg_match('/^1[34578]\d{9}$/', $value);
    }

    public static function validate_integer($value, $params, $data)
    {
        return filter_var($value, FILTER_VALIDATE_INT) !== false;
    }

    public static function validate_float($value, $params, $data)
    {
        return filter_var($value, FILTER_VALIDATE_FLOAT) !== false;
    }

    public static function validate_boolean($value, $params, $data)
    {
        return filter_var($value, FILTER_VALIDATE_BOOLEAN) !== false;
    }

    public static function validate_array($value, $params, $data)
    {
        return is_array($value);
    }

    /**
     * Validates that the value contains only alphabetical characters.
     * 验证该值是否仅包含字母。
     */
    public static function validate_alpha($value, $params, $data)
    {
        return ctype_alpha($value);
    }

    public static function validate_alphanumeric($value, $params, $data)
    {
        return ctype_alnum($value);
    }

    /**
     * example:
     * $rules = [
     *      'password' => ['required', 'min:6'],
     *      'confirm_password' => ['required', 'same:password'],
     * ];
     */
    public static function validate_same($value, $params, $data)
    {
        if (!isset($params[0])) {
            throw new \Exception("Validation rule 'same' requires a parameter.");
        }

        $other_field = $params[0];

        return isset($data[$other_field]) && $value === $data[$other_field];
    }

    public static function validate_different($value, $params, $data)
    {
        if (!isset($params[0])) {
            throw new \Exception("Validation rule 'different' requires a parameter.");
        }

        $other_field = $params[0];

        return !isset($data[$other_field]) || $value !== $data[$other_field];
    }

    public static function validate_url($value, $params, $data)
    {
        return filter_var($value, FILTER_VALIDATE_URL) !== false;
    }

    public static function validate_ip($value, $params, $data)
    {
        return filter_var($value, FILTER_VALIDATE_IP) !== false;
    }

    /**
     * example:
     * $rules = ['gender' => ['required', 'in:male,female'],];
     */
    public static function validate_in($value, $params, $data)
    {
        if (!isset($params[0])) {
            throw new \Exception("Validation rule 'in' requires a parameter.");
        }

        $allowed_values = $params;

        return in_array($value, $allowed_values);
    }

    public static function validate_not_in($value, $params, $data)
    {
        if (!isset($params[0])) {
            throw new \Exception("Validation rule 'not_in' requires a parameter.");
        }

        $disallowed_values = $params;

        return !in_array($value, $disallowed_values);
    }

    public static function validate_uppercase($value, $params, $data)
    {
        return preg_match('/[A-Z]/', $value);
    }

    public static function validate_lowercase($value, $params, $data)
    {
        return preg_match('/[a-z]/', $value);
    }

    public static function validate_symbol($value, $params, $data)
    {
        return preg_match('/[^\w\s]/', $value);
    }

    public static function validate_regex($value, $params, $data)
    {
        if (!isset($params[0])) {
            throw new \Exception("Validation rule 'regex' requires a parameter.");
        }

        $pattern = $params[0];

        return preg_match($pattern, $value);
    }
}
