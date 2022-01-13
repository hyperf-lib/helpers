<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
namespace Helpers\Request;

use GuzzleHttp\Client;
use Helpers\Log;
use Hyperf\Guzzle\HandlerStackFactory;

/**
 * Class HttpClient.
 * @method array get(string $uri, array $params, string $type = "")
 * @method array head(string $uri, array $params, string $type = "")
 * @method array put(string $uri, array $params, string $type = "")
 * @method array patch(string $uri, array $params, string $type = "")
 * @method array delete(string $uri, array $params, string $type = "")
 * @method array getAsync(string $uri, array $params, string $type = "")
 * @method array headAsync(string $uri, array $params, string $type = "")
 * @method array putAsync(string $uri, array $params, string $type = "")
 * @method array postAsync(string $uri, array $params, string $type = "")
 * @method array patchAsync(string $uri, array $params, string $type = "")
 * @method array deleteAsync(string $uri, array $params, string $type = "")
 */
class HttpClient
{
    public const MAX_CONNECTIONS = 100;

    public const MIN_CONNECTIONS = 5;

    // clientFactory
    private $clientFactory;

    public function __construct()
    {
        $this->clientFactory = $this->getClient();
    }

    /**
     * @param string $method
     * @param array $params
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Throwable
     */
    public function __call($method, $params): string
    {
        try {
            Log::info('request:' . $method, $params);
            $response = $this->clientFactory->{$method}(...$params);
            $context = $response->getBody()
                ->getContents();
            Log::info('response', ['context' => $context]);

            return $context;
        } catch (\Throwable $e) {
            Log::error('error:', formatThrowable($e));
            throw $e;
        }
    }

    /**
     * @return Client
     */
    private function getClient()
    {
        $factory = new HandlerStackFactory();
        $stack = $factory->create([
            'max_connections' => self::MAX_CONNECTIONS,
            'min_connections' => self::MIN_CONNECTIONS,
            'wait_timeout' => 1.0,
        ]);

        return make(Client::class, [
            'config' => [
                'handler' => $stack,
                'timeout' => 3.0,
            ],
        ]);
    }
}
