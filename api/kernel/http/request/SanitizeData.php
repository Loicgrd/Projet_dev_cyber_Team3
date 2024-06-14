<?php
/**
 * Http Request processing
 * @author Aélion <s36092@stagiaire.aelion.fr>
 * @version 1.0.0
 *  - sanitize data before sending to database
 */
namespace Aelion\Http\Request;

final Class SanitizeData {
    public function sanitizeValue($value){
        $_sanitizeValue = filter_var($value, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        return $_sanitizeValue;
    }
}