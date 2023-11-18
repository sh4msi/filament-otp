<?php

namespace Sh4msi\FilamentOtp\Utility;

use Sh4msi\FilamentOtp\interface\TokenGeneratorInterface;

class TokenGenerator implements TokenGeneratorInterface
{
    public function getToken($length = 5): string
    {
        if (config('filament-otp.token_type') == 'number') {
            return $this->generateRandomNumber($length);
        }

        if (config('filament-otp.token_type') == 'etc') {
            return $this->generateRandomToken($length);
        }

        return $this->generateRandomAlphabet($length);
    }

    private function generateRandomAlphabet($length): string
    {
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = substr(str_shuffle($characters), 0, $length);

        $alphabetLength = strlen($randomString);
        if ($alphabetLength < $length) {
            $randomString .= $this->generateRandomAlphabet($length - $alphabetLength);
        }

        return $randomString;
    }

    private function generateRandomNumber($length): string
    {
        $min = pow(10, $length - 1);
        $max = pow(10, $length) - 1;

        return (string) mt_rand($min, $max);
    }

    private function generateRandomToken($length): string
    {
        $length = ceil($length / 2);
        $token = $this->generateRandomAlphabet($length);
        $token .= $this->generateRandomNumber($length);

        return substr(str_shuffle($token), 0, $length);
    }
}
