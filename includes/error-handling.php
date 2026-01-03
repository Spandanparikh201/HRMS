<?php
// Error Handling Functions
function handleDatabaseError($query, $operation = 'database operation') {
    if (!$query) {
        error_log("Database error during $operation: " . print_r($dbh->errorInfo(), true));
        return false;
    }
    return true;
}

function sanitizeOutput($data) {
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

function validateInput($data, $type = 'string') {
    switch($type) {
        case 'int':
            return filter_var($data, FILTER_VALIDATE_INT);
        case 'email':
            return filter_var($data, FILTER_VALIDATE_EMAIL);
        default:
            return trim(strip_tags($data));
    }
}
?>