# Changelog

All notable changes to `filament-otp` will be documented in this file.

## v4.2.0 - 2026-10-08

### Added
- Multi-panel support with panel-scoped route names (`filament-otp.{panelId}.login` and `filament-otp.{panelId}.confirm`).
- Configurable `'panels'` option in `config/filament-otp.php` to restrict OTP routes to designated panel IDs.
- Automatic panel detection via `FilamentOtpPlugin` registration (`$panel->hasPlugin('filament-otp')`).
- Helper methods on `FilamentOtp` service and facade: `getLoginUrl()`, `getConfirmUrl()`, `getLoginRouteName()`, `getConfirmRouteName()`, and `getCurrentPanelId()`.
- Multi-panel route registration and serialization test suite (`tests/Feature/MultiPanelRoutesTest.php`).

### Fixed
- Fixed route serialization collision (`Unable to prepare route [login/otp/confirm] for serialization. Another route has already been assigned name [filament-otp.confirm]`) when multiple panels are present.
- Dynamically resolved redirect targets in `LoginOTP`, `ConfirmOTP`, and `TokenGuard` based on the current Filament panel.
- Updated `login-otp-btn.blade.php` to dynamically link to the current panel's OTP login route.

## v4.1.0 - 2026-10-03

### Added
- Official support for **Laravel 13** alongside Laravel 11 and 12.

## v4.0.0 - 2026-10-03

### Added
- Official support for **Filament v5** alongside Filament v3 and v4.
- Support for `livewire:init` and `window.Livewire` in countdown timer for SPA navigation and Livewire 4 compatibility.

### Changed
- Normalized event payload handling in `resend-token` component for Livewire 4.
- Localized countdown resend button state using package translation keys.

### Fixed
- Removed trailing comment syntax artifact in `login-otp-btn.blade.php`.

## v3.0.1 - 2026-10-02

### Fixed
- Fixed Filament v3 compatibility by overriding `getView()` method instead of `$view` property.
- Fixed panel resolution across Filament v3 and v4 using `getCurrentPanel()` and `getDefaultPanel()`.
- Removed unused `spatie/laravel-ray` dependency and faked notifications in test setup.

## v3.0.0 - 2026-10-02

### Added
- Official support for **Filament v4** alongside **Filament v3**.
- Dynamic `LoginResponse` contract resolution across Filament v3 and v4 namespaces.
- Dynamic panel login route resolution in Livewire components (`getLoginUrl()`).
- Comprehensive **Pest 3** test suite covering Unit, Feature, and Architecture tests (54 tests, 213 assertions).
- Static analysis with **PHPStan** (level 4) and custom test fixtures.

### Changed
- Updated default OTP token length (`token_count`) from 5 to 6 digits.
- Replaced `<x-filament-panels::form>` in Blade views with standard `<form wire:submit="authenticate">` for cross-version compatibility.
- Decoupled `TokenListener` to depend on `TokenGeneratorInterface` contract instead of concrete implementation.
- Upgraded dev dependencies (`pestphp/pest: ^3.0`, `orchestra/testbench: ^9.0 || ^10.0`).

### Security
- Migrated token generation from `mt_rand()` / `str_shuffle()` to CSPRNG `random_int()`.
- Mitigated authentication bypass by enforcing timing-safe `hash_equals()` and strict non-empty session token validation.
- Ensured complete session cleanup (`loginId`, `token`, `token_expiry`) post-authentication and within `TokenGuard`.
- Added null-safe guards for authenticatable user instances during token resend operations.

## v2.0.0 - 2025-09-13

support Laravel 11 and 12

## 1.0.0 - 202X-XX-XX

- initial release
