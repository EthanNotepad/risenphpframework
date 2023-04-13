<?php

namespace libs\Core;

class Validator extends ValidateRules
{
    private $data;
    private $rules;
    private $messages;
    private $fields;

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

            // Only validate the field if it has been set
            if (!empty($this->fields) && !in_array($field, $this->fields)) {
                continue;
            }

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

                if (!$this->$method($value, $params, $this->data)) {
                    $message = isset($this->messages["$field.$rule"]) ? $this->messages["$field.$rule"] : "The $field field is invalid.";
                    $errors[$field] = $message;

                    // Stop validating this field if it fails
                    break;
                }
            }
        }

        return count($errors) ? $errors : true;
    }

    public function setFields(array $value)
    {
        $this->fields = $value;
        return $this;
    }
}
