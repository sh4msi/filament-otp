<?php

return [
    'login-btn-text' => 'Login without password',

    'login' => [
        'heading' => 'Login with a one-time password',

        'fields' => [
            'loginId' => [
                'label' => 'user name',
            ],
        ],

        'buttons' => [
            'submit' => [
                'label' => 'Validation',
            ],
        ],

        'messages' => [
            'token_sent' => 'We have sent you a login code to login.',
        ],
    ],

    'confirm' => [
        'heading' => 'Login with a one-time password',

        'fields' => [
            'token' => [
                'label' => 'login code',
            ],
        ],

        'buttons' => [
            'submit' => [
                'label' => 'Login',
            ],
            'sign_in' => [
                'label' => 'Login with username and password',
            ],
            'resend' => [
                'help_text' => 'Did you not receive the login code?',
                'label' => 'Resend',
            ],
        ],

        'messages' => [
            'token_resent' => 'The login code has been successfully re-sent to you.',
            'throttled' => 'You have sent more requests than allowed. Please try :seconds more seconds.',
        ],
    ],
];
