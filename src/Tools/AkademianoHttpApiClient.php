<?php
declare(strict_types=1);

namespace Akademiano\HttpWarp\Tools;

use Akademiano\Utils\Parts\LoggerTrait;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Client;
use GuzzleHttp\Promise\PromiseInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LogLevel;

class AkademianoHttpApiClient
{
    use LoggerTrait;

    const CONFIG_API_ENDPOINT_NAME = "apiEndpoint";

    /** @var  Client */
    protected $httpClient;

    /** @var  array */
    protected $config;

    protected $sendApiEndpoint;

    protected $apiKey;

    /**
     * HttpApiClient constructor.
     * @param Client $httpClient
     * @param array $config
     * @param $apiKey
     */
    public function __construct(array $config = [], string $apiKey = null)
    {
        $this->config = $config;
        $this->apiKey = $apiKey;
    }


    /**
     * @return array
     */
    public function getConfig(): ?array
    {
        return $this->config;
    }

    /**
     * @param array $config
     */
    public function setConfig(array $config)
    {
        $this->config = $config;
    }

    public function getSendApiEndpoint(): array
    {
        if (null === $this->sendApiEndpoint) {
            $config = $this->getConfig();
            $default = [
                'timeout' => 10,
                'headers' => [
                    'User-Agent' => 'Akademiano/Api/HttpClient/1.0',
                    'Accept' => 'application/json',
                ]
            ];
            if (!isset($config[self::CONFIG_API_ENDPOINT_NAME])) {
                $this->sendApiEndpoint = $default;
            } else {
                $this->sendApiEndpoint = array_merge_recursive($config[self::CONFIG_API_ENDPOINT_NAME], $default);
            }
        }
        return $this->sendApiEndpoint;
    }

    /**
     * @return mixed
     */
    protected function getApiKey():?string
    {
        return $this->apiKey;
    }

    /**
     * @param mixed $apiKey
     */
    public function setApiKey(string $apiKey)
    {
        $this->apiKey = $apiKey;
    }

    /**
     * @return Client
     */
    public function getHttpClient(): Client
    {
        if (null === $this->httpClient) {
            $apiEndpoint = $this->getSendApiEndpoint();
            $this->httpClient = new Client($apiEndpoint);
        }
        return $this->httpClient;
    }

    /**
     * @param Client $httpClient
     */
    public function setHttpClient(Client $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function parseResponse(string $body):?array
    {
        try {
            $data = json_decode($body, true);
        } catch (\Throwable $e) {
            return null;
        }
        return $data;
    }

    public function send(
        $method, $uri = '', array $sendOptions = [],
        $logId,
        &$result,
        callable $onOk = null,
        callable $onError = null,
        callable $onNotFound = null,
        array $promiseParams = []
    ): PromiseInterface
    {
        $httpClient = $this->getHttpClient();

        $sendOptions['query']['apikey'] = $this->getApiKey();

        $promise = $httpClient->requestAsync($method, $uri, $sendOptions);

        $promise->then(
            function (ResponseInterface $res) use ($onOk, $onError, $promiseParams, $logId, &$result) {
                $body = $res->getBody();
                $result = $this->parseResponse((string)$body);
                if ($res->getStatusCode() === 200) {
                    if ($result && 'OK' === ($result['status'] ?? false)) {
                        if ($onOk) {
                            call_user_func($onOk, $promiseParams);
                        }
                        $this->log(LogLevel::INFO, sprintf('Data %s is send', $logId));
                    }
                }
                if ($onError) {
                    call_user_func($onError, $promiseParams);
                }
                $this->log(
                    LogLevel::ERROR,
                    sprintf('Error in send %s: - status code %s', $logId, $res->getStatusCode()),
                    is_array($result) ? $result : ['text' => (string)$body]
                );
            },
            function (RequestException $e) use ($onError, $onNotFound, $promiseParams, $logId, &$result) {
                $code = $e->getResponse()->getStatusCode();
                switch ($code) {
                    case 404:
                        if ($onNotFound) {
                            call_user_func($onNotFound, $promiseParams);
                        }
                        break;
                    default:
                        if ($onError) {
                            call_user_func($onError, $promiseParams);
                        }
                }
                $body = $e->getResponse()->getBody()->getContents();
                $result = $this->parseResponse((string)$body);
                $this->log(
                    LogLevel::ERROR,
                    sprintf('Error in message %s: - %s', $logId, $e->getMessage()),
                    is_array($result) ? $result : ['text' => (string)$body]
                );
            }
        );
        return $promise;
    }
}
