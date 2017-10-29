<?php
declare(strict_types=1);

namespace Akademiano\EntityOperator\Ext\Tools\Http;

use Akademiano\HttpWarp\Tools\AkademianoHttpApiClient;
use Akademiano\Utils\Parts\LoggerTrait;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Client;
use GuzzleHttp\Promise\PromiseInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LogLevel;

/**
 * Class HttpApiClient
 * @package Akademiano\EntityOperator\Ext\Tools\Http
 * @deprecated Moved to akademiano/httpwarp
 */
class HttpApiClient extends AkademianoHttpApiClient
{

}
