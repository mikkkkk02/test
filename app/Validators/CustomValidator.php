<?php

namespace App\Validators;

class CustomValidator {

    public function validateAlphaSpaces($attribute, $value) {
		return preg_match('/^[\pL\s]+$/u', $value);
    }
}