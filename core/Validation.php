<?php
/**
 * SMARTRESTA Server-Side Input Validation Engine
 * Enforces Zero Invalid Input Policy across all controllers & API endpoints
 */

class Validation {
    private $errors = [];

    public function require($data, $field, $fieldName = null) {
        $label = $fieldName ?: $field;
        if (!isset($data[$field]) || trim((string)$data[$field]) === '') {
            $this->errors[$field] = "The {$label} field is required.";
        }
        return $this;
    }

    public function numeric($data, $field, $min = null, $fieldName = null) {
        $label = $fieldName ?: $field;
        if (isset($data[$field])) {
            $val = $data[$field];
            if (!is_numeric($val)) {
                $this->errors[$field] = "The {$label} must be a valid number.";
            } elseif ($min !== null && $val < $min) {
                $this->errors[$field] = "The {$label} must be at least {$min}.";
            }
        }
        return $this;
    }

    public function inList($data, $field, array $allowedList, $fieldName = null) {
        $label = $fieldName ?: $field;
        if (isset($data[$field]) && !in_array($data[$field], $allowedList, true)) {
            $this->errors[$field] = "The selected {$label} is invalid.";
        }
        return $this;
    }

    public function isValid() {
        return empty($this->errors);
    }

    public function getErrors() {
        return $this->errors;
    }
}
