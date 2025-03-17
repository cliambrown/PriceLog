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

if (!function_exists('simplify_string')) {
    function simplify_string($str) {
        return \Illuminate\Support\Str::lower(
            \Illuminate\Support\Str::transliterate($str)
        );
    }
}