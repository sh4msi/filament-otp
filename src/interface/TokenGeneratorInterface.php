<?php

namespace Sh4msi\FilamentOtp\interface;

interface TokenGeneratorInterface
{
    public function getToken(int $length): string;
}
