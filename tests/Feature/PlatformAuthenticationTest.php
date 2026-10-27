<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class PlatformAuthenticationTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_request_without_platform_identity_is_rejected_in_secure_mode(): void
    {
        config()->set('henrik.auth.allow_local_identity', false);
        config()->set('henrik.auth.shared_secret', 'testing-secret');

        $this->get('/')->assertUnauthorized();
    }

    public function test_signed_platform_identity_is_accepted_and_displayed(): void
    {
        config()->set('henrik.auth.allow_local_identity', false);
        config()->set('henrik.auth.shared_secret', 'testing-secret');
        $timestamp = (string) now()->timestamp;
        $payload = implode('|', [$timestamp, 'entra-123', 'erika@donihue.cl', 'Erika']);

        $this->withHeaders([
            'X-Henrik-User-Email' => 'erika@donihue.cl',
            'X-Henrik-User-Name' => 'Erika',
            'X-Henrik-User-Id' => 'entra-123',
            'X-Henrik-Timestamp' => $timestamp,
            'X-Henrik-Signature' => hash_hmac('sha256', $payload, 'testing-secret'),
        ])->get('/')->assertOk()->assertSee('erika@donihue.cl');
    }

    public function test_expired_signature_is_rejected(): void
    {
        config()->set('henrik.auth.allow_local_identity', false);
        config()->set('henrik.auth.shared_secret', 'testing-secret');
        $timestamp = (string) now()->subMinutes(10)->timestamp;
        $payload = implode('|', [$timestamp, 'entra-123', 'erika@donihue.cl', 'Erika']);

        $this->withHeaders([
            'X-Henrik-User-Email' => 'erika@donihue.cl',
            'X-Henrik-User-Name' => 'Erika',
            'X-Henrik-User-Id' => 'entra-123',
            'X-Henrik-Timestamp' => $timestamp,
            'X-Henrik-Signature' => hash_hmac('sha256', $payload, 'testing-secret'),
        ])->get('/')->assertUnauthorized();
    }
}
