<?php

namespace libs\Core;

class Validator
{
    private $data;
    private $rules;
    private $messages;

    public function __construct($data, $rules, $messages = [])
    {
        $this->data = $data;
        $this->rules = $rules;
        $this->messages = $messages;
    }

    public function validate()
    {
        $errors = [];

        foreach ($this->rules as $field => $rules) {
            foreach ($rules as $rule) {
                $value = $this->data[$field];
                $params = null;

                if (strpos($rule, ':') !== false) {
                    list($rule, $params) = explode(':', $rule, 2);
                    $params = explode(',', $params);
                }

                $method = 'validate_' . $rule;

                if (!method_exists($this, $method)) {
                    Message::send(412, [], "Invalid validation rule: $rule");
                }

                if (!$this->$method($value, $params)) {
                    $message = isset($this->messages["$field.$rule"]) ? $this->messages["$field.$rule"] : "The $field field is invalid.";
                    $errors[$field] = $message;

                    // Stop validating this field if it fails
                    break;
                }
            }
        }

        return count($errors) ? $errors : true;
    }

    private function validate_required($value, $params)
    {
        return !empty($value);
    }

    private function validate_min($value, $params)
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

    private function validate_max($value, $params)
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

    private function validate_numeric($value, $params)
    {
        if (empty($value)) {
            return true;
        }
        return is_numeric($value);
    }

    private function validate_date($value, $params)
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

    private function validate_email($value, $params)
    {
        if (empty($value)) {
            return true;
        }
        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }

    private function validate_integer($value, $params)
    {
        if (empty($value)) {
            return true;
        }
        return filter_var($value, FILTER_VALIDATE_INT) !== false;
    }

    private function validate_float($value, $params)
    {
        if (empty($value)) {
            return true;
        }
        return filter_var($value, FILTER_VALIDATE_FLOAT) !== false;
    }

    private function validate_boolean($value, $params)
    {
        if (empty($value)) {
            return true;
        }
        return filter_var($value, FILTER_VALIDATE_BOOLEAN) !== false;
    }

    private function validate_array($value, $params)
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
    private function validate_alpha($value, $params)
    {
        if (empty($value)) {
            return true;
        }
        return ctype_alpha($value);
    }

    private function validate_alphanumeric($value, $params)
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
    private function validate_same($value, $params)
    {
        if (!isset($params[0])) {
            Message::send(412, [], "Validation rule 'same' requires a parameter.");
        }

        $other_field = $params[0];

        return isset($this->data[$other_field]) && $value === $this->data[$other_field];
    }

    private function validate_different($value, $params)
    {
        if (!isset($params[0])) {
            Message::send(412, [], "Validation rule 'different' requires a parameter.");
        }

        $other_field = $params[0];

        return !isset($this->data[$other_field]) || $value !== $this->data[$other_field];
    }

    private function validate_url($value, $params)
    {
        if (empty($value)) {
            return true;
        }
        return filter_var($value, FILTER_VALIDATE_URL) !== false;
    }

    private function validate_ip($value, $params)
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
    private function validate_in($value, $params)
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

    private function validate_not_in($value, $params)
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

    // add more validation methods here
}
