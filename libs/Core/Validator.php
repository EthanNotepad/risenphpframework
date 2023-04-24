<?php

namespace libs\Core;

class Validator extends ValidateRules
{
    private $data;
    private $rules;
    private $messages;
    private $fields;
    private $alias = [
        'numeric' => ['number', 'num'],
        'symbol' => ['specialcharacter'],
    ];

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
                // If a field rule is set, but there is no data to be validated for this field, then skip
                if (!isset($this->data[$field])) {
                    continue;
                }

                // Only validate the field if it has been set
                if (!empty($this->fields) && !in_array($field, $this->fields)) {
                    continue;
                }

                $value = $this->data[$field];
                $params = null;

                if (strpos($rule, ':') !== false) {
                    list($rule, $params) = explode(':', $rule, 2);
                    $params = explode(',', $params);
                }

                $method = 'validate_' . $rule;

                if (!method_exists($this, $method)) {
                    // Add an alias system, if the verification rule cannot be found, 
                    // the alias will be searched, and if it exists, the defined rule will be used
                    $alias_matched = false;
                    foreach ($this->alias as $alias_key => $alias_array) {
                        if (in_array($rule, $alias_array)) {
                            $method = 'validate_' . $alias_key;
                            $alias_matched = true;
                            break;
                        }
                    }
                    if (!$alias_matched) {
                        throw new \Exception("Invalid validation rule: $rule");
                    }
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
