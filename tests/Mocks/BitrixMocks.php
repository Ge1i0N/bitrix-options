<?php

declare(strict_types=1);

// Global function mocks for Bitrix CMS

if (!function_exists('check_bitrix_sessid')) {
    function check_bitrix_sessid(): bool
    {
        return true;
    }
}

if (!function_exists('bitrix_sessid_post')) {
    function bitrix_sessid_post(): string
    {
        return '';
    }
}
