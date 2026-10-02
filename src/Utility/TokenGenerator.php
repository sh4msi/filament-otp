<?php

namespace Sh4msi\FilamentOtp\Utility;

use Sh4msi\FilamentOtp\interface\TokenGeneratorInterface;

class TokenGenerator implements TokenGeneratorInterface
{
    public function getToken(int $length = 5): string
    {
        $length = max(1, $length);

        if (config('filament-otp.token_type') === 'number') {
            return $this->generateRandomNumber($length);
        }

        if (config('filament-otp.token_type') === 'etc') {
            return $this->generateRandomToken($length);
        }

        return $this->generateRandomAlphabet($length);
    }

    private function generateRandomAlphabet(int $length): string
    {
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $max = strlen($characters) - 1;
        $result = '';

        for ($i = 0; $i < $length; $i++) {
            $result .= $characters[random_int(0, $max)];
        }

        return $result;
    }

    private function generateRandomNumber(int $length): string
    {
        $result = '';

        for ($i = 0; $i < $length; $i++) {
            $result .= (string) random_int(0, 9);
        }

        return $result;
    }

    private function generateRandomToken(int $length): string
    {
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $max = strlen($characters) - 1;
        $result = '';

        for ($i = 0; $i < $length; $i++) {
            $result .= $characters[random_int(0, $max)];
        }

        return $result;
    }
}
