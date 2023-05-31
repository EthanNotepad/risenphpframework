<?php

namespace libs\Core;

class Validator
{
    private $data;
    private $rules;
    private $messages;
    private $fields = [];

    public function __construct(array $data, array $rules, array $messages = [])
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
            if ($this->shouldSkipField($field)) {
                continue;
            }

            // Get the value of the field being validated
            $value = isset($this->data[$field]) ? $this->data[$field] : null;

            // skip validation if value is empty and the field is not required
            if (empty($value) && !in_array('required', $rules)) {
                continue;
            }

            foreach ($rules as $rule) {
                // Get the validation rule and params from the rule string
                $validationRule = ValidateRules::getValidationRule($rule);
                if ($validationRule === false) {
                    throw new \Exception("Invalid validation rule: $rule");
                }
                $params = $validationRule['params'];
                $rule = $validationRule['rule'];

                // Call the validation method with the value, params, and data
                if (!$validationRule['method']($value, $params, $this->data)) {
                    $message = $this->getValidationMessage($field, $rule, $params);
                    $errors[$field] = $message;
                    break; // Stop validating this field if it fails
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

    private function shouldSkipField(string $field)
    {
        return !empty($this->fields) && !in_array($field, $this->fields);
    }

    private function getValidationMessage(string $field, string $rule, array $params)
    {
        $messageKey = "$field.$rule";
        $defaultMessage = ValidateRules::getDefaultMessage($field, $rule, $params);
        return isset($this->messages[$messageKey]) ? $this->messages[$messageKey] : $defaultMessage;
    }
}
