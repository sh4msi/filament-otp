<?php

namespace Sh4msi\FilamentOtp\Events;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TokenSent
{
    use Dispatchable;
    use SerializesModels;

    /**
     * The authenticated user.
     */
    public Authenticatable $user;

    /**
     * Create a new event instance.
     */
    public function __construct(Authenticatable $user)
    {
        $this->user = $user;
    }
}
