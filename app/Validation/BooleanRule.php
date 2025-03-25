<?php 

namespace App\Validation;

use CodeIgniter\Validation\Rules;

class BooleanRule extends Rules
{
    /**
     * Checks if the value is a valid boolean representation
     * 
     * @param string|null $str Value to check
     * 
     * @return bool
     */
    public function boolean($str): bool
    {
        if ($str === null) {
            return false;
        }
        return filter_var($str, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) !== null;
    }
}