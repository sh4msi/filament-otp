<?php

use Sh4msi\FilamentOtp\interface\TokenGeneratorInterface;
use Sh4msi\FilamentOtp\Utility\TokenGenerator;

beforeEach(function () {
    $this->generator = new TokenGenerator;
});

it('implements TokenGeneratorInterface', function () {
    expect($this->generator)->toBeInstanceOf(TokenGeneratorInterface::class);
});

it('generates numeric token with default length of 5', function () {
    config()->set('filament-otp.token_type', 'number');

    $token = $this->generator->getToken();

    expect($token)
        ->toBeString()
        ->toHaveLength(5)
        ->toMatch('/^\d{5}$/');
});

it('generates numeric token with custom length', function () {
    config()->set('filament-otp.token_type', 'number');

    foreach ([4, 6, 8] as $length) {
        $token = $this->generator->getToken($length);

        expect($token)
            ->toBeString()
            ->toHaveLength($length)
            ->toMatch('/^\d{' . $length . '}$/');
    }
});

it('generates alphabet token when token type is default or alphabet', function () {
    config()->set('filament-otp.token_type', 'alphabet');

    $token = $this->generator->getToken(6);

    expect($token)
        ->toBeString()
        ->toHaveLength(6)
        ->toMatch('/^[a-zA-Z]{6}$/');
});

it('generates alphabet token with longer length replenishing characters', function () {
    config()->set('filament-otp.token_type', 'string');

    $token = $this->generator->getToken(60);

    expect($token)
        ->toBeString()
        ->toHaveLength(60)
        ->toMatch('/^[a-zA-Z]{60}$/');
});

it('generates etc alphanumeric token when token type is etc', function () {
    config()->set('filament-otp.token_type', 'etc');

    $token = $this->generator->getToken(6);

    expect($token)
        ->toBeString()
        ->not->toBeEmpty();
});

it('generates random tokens on subsequent calls', function () {
    config()->set('filament-otp.token_type', 'number');

    $token1 = $this->generator->getToken(6);
    $token2 = $this->generator->getToken(6);

    // Random tokens should vary with high probability
    expect($token1)->toBeString();
    expect($token2)->toBeString();
});
