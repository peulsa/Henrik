<?php

namespace App\Http\Middleware;

use App\Support\PlatformUser;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticatePlatformRequest
{
    public function handle(Request $request, Closure $next): Response
    {
        $email = trim((string) $request->header('X-Henrik-User-Email'));
        $name = trim((string) $request->header('X-Henrik-User-Name')) ?: null;
        $externalId = trim((string) $request->header('X-Henrik-User-Id')) ?: null;

        if ($this->allowsLocalIdentity() && $email === '') {
            $email = (string) config('henrik.auth.local_email');
            $name = (string) config('henrik.auth.local_name');
            $externalId = 'local-development';
        } elseif (! $this->hasValidSignature($request, $email, $name, $externalId)) {
            abort(401, 'La plataforma principal no entregó una identidad válida.');
        }

        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            abort(401, 'El correo de la identidad recibida no es válido.');
        }

        $request->attributes->set('platform_user', new PlatformUser(
            email: mb_strtolower($email),
            name: $name,
            externalId: $externalId,
        ));

        return $next($request);
    }

    private function allowsLocalIdentity(): bool
    {
        return (bool) config('henrik.auth.allow_local_identity')
            && app()->environment(['local', 'testing']);
    }

    private function hasValidSignature(Request $request, string $email, ?string $name, ?string $externalId): bool
    {
        $timestamp = (string) $request->header('X-Henrik-Timestamp');
        $signature = (string) $request->header('X-Henrik-Signature');
        $secret = (string) config('henrik.auth.shared_secret');

        if ($email === '' || $timestamp === '' || $signature === '' || $secret === '') {
            return false;
        }

        if (! ctype_digit($timestamp) || abs(now()->timestamp - (int) $timestamp) > 300) {
            return false;
        }

        $payload = implode('|', [$timestamp, $externalId ?? '', $email, $name ?? '']);
        $expected = hash_hmac('sha256', $payload, $secret);

        return hash_equals($expected, $signature);
    }
}
