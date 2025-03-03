<?php

if (!function_exists('get_request_boolean')) {
    function get_request_boolean($value) {
        return (
            $value === true
            || $value === 'true'
            || $value === 1
            || $value === '1'
        );
    }
}