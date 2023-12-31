<?php

return [
    'login-btn-text' => 'Login without password',

    'login' => [
        'heading' => 'ورود با رمز یک بار مصرف',

        'fields' => [
            'loginId' => [
                'label' => 'شناسه کاربری',
            ],
        ],

        'buttons' => [
            'submit' => [
                'label' => 'صحت سنجی',
            ],
        ],

        'messages' => [
            'token_sent' => 'ما یک کد ورود برای شما ارسال کرده ایم تا وارد شوید.',
        ],
    ],

    'confirm' => [
        'heading' => 'ورود با رمز یک بار مصرف',

        'fields' => [
            'token' => [
                'label' => 'کد ورود',
            ],
        ],

        'buttons' => [
            'submit' => [
                'label' => 'ورود',
            ],
            'sign_in' => [
                'label' => 'ورود با نام کاربری و رمز عبور',
            ],
            'resend' => [
                'help_text' => 'کد ورود را دریافت نکردید؟',
                'label' => 'ارسال مجدد',
            ],
        ],

        'messages' => [
            'token_resent' => 'کد ورود دوباره با موفقیت برای شما ارسال شد.',
            'throttled' => 'شما بیش از حد مجاز درخواست ارسال داشته‌اید. لطفاً :seconds ثانیه دیگر تلاش کنید.',
        ],
    ],
];
