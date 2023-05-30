<?php

namespace libs\Core;

class ValidateRules
{
    public function validate_required($value, $params, $data)
    {
        if (is_numeric($value) || is_bool($value)) {
            return true;
        }
        return !empty($value);
    }

    public function validate_min($value, $params, $data)
    {
        if (empty($value)) {
            return true;
        }
        if (!isset($params[0])) {
            throw new \Exception("Validation rule 'min' requires a parameter.");
        }

        $min = intval($params[0]);

        return strlen($value) >= $min;
    }

    public function validate_max($value, $params, $data)
    {
        if (empty($value)) {
            return true;
        }
        if (!isset($params[0])) {
            throw new \Exception("Validation rule 'max' requires a parameter.");
        }

        $max = intval($params[0]);

        return strlen($value) <= $max;
    }

    public function validate_numeric($value, $params, $data)
    {
        if (empty($value)) {
            return true;
        }
        return is_numeric($value);
    }

    public function validate_double($value, $params, $data)
    {
        if (empty($value)) {
            return true;
        }
        return is_double($value);
    }

    public function validate_date($value, $params, $data)
    {
        if (empty($value)) {
            return true;
        }
        if (!isset($params[0])) {
            throw new \Exception("Validation rule 'date' requires a parameter.");
        }

        $format = $params[0];
        $date = \DateTime::createFromFormat($format, $value);

        return $date && $date->format($format) == $value;
    }

    public function validate_time($value, $params, $data)
    {
        if (empty($value)) {
            return true;
        }
        // support 000:00:00.000000 and 000:00
        $pattern = '/^(-?\d{1,3}):([0-5]\d{1,2})(:[0-5]\d{1,2})?(\.\d{1,6})?$/';

        return preg_match($pattern, $value);
    }

    public function validate_email($value, $params, $data)
    {
        if (empty($value)) {
            return true;
        }
        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }

    public function validate_phone($value, $params, $data)
    {
        if (empty($value)) {
            return true;
        }
        return preg_match('/^1[34578]\d{9}$/', $value);
    }

    public function validate_integer($value, $params, $data)
    {
        if (empty($value)) {
            return true;
        }
        return filter_var($value, FILTER_VALIDATE_INT) !== false;
    }

    public function validate_float($value, $params, $data)
    {
        if (empty($value)) {
            return true;
        }
        return filter_var($value, FILTER_VALIDATE_FLOAT) !== false;
    }

    public function validate_boolean($value, $params, $data)
    {
        if (empty($value)) {
            return true;
        }
        return filter_var($value, FILTER_VALIDATE_BOOLEAN) !== false;
    }

    public function validate_array($value, $params, $data)
    {
        if (empty($value)) {
            return true;
        }
        return is_array($value);
    }

    /**
     * Validates that the value contains only alphabetical characters.
     * 验证该值是否仅包含字母。
     */
    public function validate_alpha($value, $params, $data)
    {
        if (empty($value)) {
            return true;
        }
        return ctype_alpha($value);
    }

    public function validate_alphanumeric($value, $params, $data)
    {
        if (empty($value)) {
            return true;
        }
        return ctype_alnum($value);
    }

    /**
     * example:
     * $rules = [
     *      'password' => ['required', 'min:6'],
     *      'confirm_password' => ['required', 'same:password'],
     * ];
     */
    public function validate_same($value, $params, $data)
    {
        if (!isset($params[0])) {
            throw new \Exception("Validation rule 'same' requires a parameter.");
        }

        $other_field = $params[0];

        return isset($data[$other_field]) && $value === $data[$other_field];
    }

    public function validate_different($value, $params, $data)
    {
        if (!isset($params[0])) {
            throw new \Exception("Validation rule 'different' requires a parameter.");
        }

        $other_field = $params[0];

        return !isset($data[$other_field]) || $value !== $data[$other_field];
    }

    public function validate_url($value, $params, $data)
    {
        if (empty($value)) {
            return true;
        }
        return filter_var($value, FILTER_VALIDATE_URL) !== false;
    }

    public function validate_ip($value, $params, $data)
    {
        if (empty($value)) {
            return true;
        }
        return filter_var($value, FILTER_VALIDATE_IP) !== false;
    }

    /**
     * example:
     * $rules = ['gender' => ['required', 'in:male,female'],];
     */
    public function validate_in($value, $params, $data)
    {
        if (empty($value)) {
            return true;
        }
        if (!isset($params[0])) {
            throw new \Exception("Validation rule 'in' requires a parameter.");
        }

        $allowed_values = $params;

        return in_array($value, $allowed_values);
    }

    public function validate_not_in($value, $params, $data)
    {
        if (empty($value)) {
            return true;
        }
        if (!isset($params[0])) {
            throw new \Exception("Validation rule 'not_in' requires a parameter.");
        }

        $disallowed_values = $params;

        return !in_array($value, $disallowed_values);
    }

    public function validate_uppercase($value, $params, $data)
    {
        if (empty($value)) {
            return true;
        }
        return preg_match('/[A-Z]/', $value);
    }

    public function validate_lowercase($value, $params, $data)
    {
        if (empty($value)) {
            return true;
        }
        return preg_match('/[a-z]/', $value);
    }

    public function validate_symbol($value, $params, $data)
    {
        if (empty($value)) {
            return true;
        }
        return preg_match('/[^\w\s]/', $value);
    }

    // add more validation methods here
}
