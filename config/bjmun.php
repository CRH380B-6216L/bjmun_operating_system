<?php
/**
 * Copyright (C) MUNPANEL
 * This file is part of MUNPANEL System.
 *
 * Open-sourced under AGPL v3 License.
 *
 * Supervised for BJMUN Opearting System at 2022
 */

return [
    'copyright_year' => '2009-2022',
    'registration_enabled' => env('MP_REGISTRATION_ENABLED', true),
    'registration_school_changable' => env('MP_REGISTRATION_SCHOOL_CHANGABLE', true),
    'store_checkout' => env('MP_STORE_CHECKOUT', true),
    'conference_id' => env('MP_CONFERENCE_ID', null), //for debug only
    'imap_host' => env('MP_IMAP_HOST', '{mail.yiad.am:143/novalidate-cert}INBOX'),
    'mailapi_host' => env('MP_MAILAPI_HOST', 'http://mail.yiad.am/'),
    'mailapi_key' => env('MP_MAILAPI_KEY', 'api@yiad.am'),
    'mailapi_secret' => env('MP_MAILAPI_SECRET', ''),
    'icp_license' => env('MP_ICPLICENSE', '')
];
