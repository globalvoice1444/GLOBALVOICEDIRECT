<?php

namespace App\Services;

/** Outcome of one voice-gateway call. Message is safe to show; it never carries secrets. */
final class VoiceProvisionResult
{
    public function __construct(
        public readonly bool $ok,
        public readonly string $message = '',
        public readonly ?int $httpStatus = null,
        public readonly bool $touchedApiKey = false,
        public readonly bool $apiKeyOnGateway = false,
    ) {}

    public static function success(?int $httpStatus = null, bool $touchedApiKey = false, bool $apiKeyOnGateway = false): self
    {
        return new self(true, '', $httpStatus, $touchedApiKey, $apiKeyOnGateway);
    }

    public static function failure(string $message, ?int $httpStatus = null): self
    {
        return new self(false, $message, $httpStatus);
    }
}
