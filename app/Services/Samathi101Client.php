<?php

namespace App\Services;

/**
 * §3.1/หมายเหตุ: Samathi101 is the source of truth for user identity —
 * login should resolve against it instead of a local password.
 *
 * ponytail: real endpoint, auth flow (redirect-based SSO vs token exchange)
 * and payload shape are still unknown, so this only returns a mock profile.
 * Replace the body with the real HTTP call once Samathi101 hands over API
 * docs; callers should keep using resolveUser() so the login flow (wherever
 * it ends up living) doesn't need to change again.
 *
 * @return array{samathi101_user_id: string, name: string, email: string, student_code: ?string}|null
 */
class Samathi101Client
{
    public function resolveUser(string $identifier): ?array
    {
        return null;
    }
}
