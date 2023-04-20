<?php

namespace libs\Core;

class ValidateRules
{
    protected function validate_required($value, $params, $data)
    {
        return !empty($value);
    }

    protected function validate_min($value, $params, $data)
    {
        if (empty($value)) {
            return true;
        }
        if (!isset($params[0])) {
            Message::send(412, [], "Validation rule 'min' requires a parameter.");
        }

        $min = intval($params[0]);

        return strlen($value) >= $min;
    }

    protected function validate_max($value, $params, $data)
    {
        if (empty($value)) {
            return true;
        }
        if (!isset($params[0])) {
            Message::send(412, [], "Validation rule 'max' requires a parameter.");
        }

        $max = intval($params[0]);

        return strlen($value) <= $max;
    }

    protected function validate_numeric($value, $params, $data)
    {
        if (empty($value)) {
            return true;
        }
        return is_numeric($value);
    }

    protected function validate_date($value, $params, $data)
    {
        if (empty($value)) {
            return true;
        }
        if (!isset($params[0])) {
            Message::send(412, [], "Validation rule 'date' requires a parameter.");
        }

        $format = $params[0];
        $date = \DateTime::createFromFormat($format, $value);

        return $date && $date->format($format) == $value;
    }

    protected function validate_email($value, $params, $data)
    {
        if (empty($value)) {
            return true;
        }
        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }

    protected function validate_integer($value, $params, $data)
    {
        if (empty($value)) {
            return true;
        }
        return filter_var($value, FILTER_VALIDATE_INT) !== false;
    }

    protected function validate_float($value, $params, $data)
    {
        if (empty($value)) {
            return true;
        }
        return filter_var($value, FILTER_VALIDATE_FLOAT) !== false;
    }

    protected function validate_boolean($value, $params, $data)
    {
        if (empty($value)) {
            return true;
        }
        return filter_var($value, FILTER_VALIDATE_BOOLEAN) !== false;
    }

    protected function validate_array($value, $params, $data)
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
    protected function validate_alpha($value, $params, $data)
    {
        if (empty($value)) {
            return true;
        }
        return ctype_alpha($value);
    }

    protected function validate_alphanumeric($value, $params, $data)
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
    protected function validate_same($value, $params, $data)
    {
        if (!isset($params[0])) {
            Message::send(412, [], "Validation rule 'same' requires a parameter.");
        }

        $other_field = $params[0];

        return isset($data[$other_field]) && $value === $data[$other_field];
    }

    protected function validate_different($value, $params, $data)
    {
        if (!isset($params[0])) {
            Message::send(412, [], "Validation rule 'different' requires a parameter.");
        }

        $other_field = $params[0];

        return !isset($data[$other_field]) || $value !== $data[$other_field];
    }

    protected function validate_url($value, $params, $data)
    {
        if (empty($value)) {
            return true;
        }
        return filter_var($value, FILTER_VALIDATE_URL) !== false;
    }

    protected function validate_ip($value, $params, $data)
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
    protected function validate_in($value, $params, $data)
    {
        if (empty($value)) {
            return true;
        }
        if (!isset($params[0])) {
            Message::send(412, [], "Validation rule 'in' requires a parameter.");
        }

        $allowed_values = $params;

        return in_array($value, $allowed_values);
    }

    protected function validate_not_in($value, $params, $data)
    {
        if (empty($value)) {
            return true;
        }
        if (!isset($params[0])) {
            Message::send(412, [], "Validation rule 'not_in' requires a parameter.");
        }

        $disallowed_values = $params;

        return !in_array($value, $disallowed_values);
    }

    protected function validate_uppercase($value, $params, $data)
    {
        if (empty($value)) {
            return true;
        }
        return preg_match('/[A-Z]/', $value);
    }

    protected function validate_lowercase($value, $params, $data)
    {
        if (empty($value)) {
            return true;
        }
        return preg_match('/[a-z]/', $value);
    }

    protected function validate_symbol($value, $params, $data)
    {
        if (empty($value)) {
            return true;
        }
        return preg_match('/[^\w\s]/', $value);
    }

    // add more validation methods here
}
