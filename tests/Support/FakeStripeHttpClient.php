<?php

namespace Tests\Support;

use Stripe\HttpClient\ClientInterface;

/**
 * In-memory Stripe HTTP client used to exercise the real Stripe PHP SDK and
 * our services without any network access. Tests provide a responder closure
 * that receives (method, path, params) and returns [body, status, headers].
 */
class FakeStripeHttpClient implements ClientInterface
{
    /** @var array<int, array{method: string, path: string, params: array}> */
    public array $requests = [];

    /** @var callable */
    public $responder;

    public function __construct(?callable $responder = null)
    {
        $this->responder = $responder ?? fn () => ['{}', 200, []];
    }

    public function request($method, $absUrl, $headers, $params, $hasFile, $apiMode = 'v1')
    {
        $path = parse_url($absUrl, PHP_URL_PATH) ?: $absUrl;

        $this->requests[] = [
            'method' => strtolower($method),
            'path' => $path,
            'params' => $params,
        ];

        return ($this->responder)($method, $path, $params);
    }

    public function requestsFor(string $method, string $pathFragment): array
    {
        return array_values(array_filter($this->requests, function ($request) use ($method, $pathFragment) {
            return $request['method'] === strtolower($method)
                && str_contains($request['path'], $pathFragment);
        }));
    }

    public function lastRequest(): ?array
    {
        return $this->requests ? end($this->requests) : null;
    }

    public static function json(array $payload): array
    {
        return [json_encode($payload), 200, []];
    }
}
