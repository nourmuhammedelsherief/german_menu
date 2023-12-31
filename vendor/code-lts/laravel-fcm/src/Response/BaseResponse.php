<?php

namespace LaravelFCM\Response;

use Psr\Http\Message\ResponseInterface;
use LaravelFCM\Response\Exceptions\ServerResponseException;
use LaravelFCM\Response\Exceptions\InvalidRequestException;
use LaravelFCM\Response\Exceptions\UnauthorizedRequestException;
use Monolog\Logger;

abstract class BaseResponse
{
    const SUCCESS = 'success';
    const FAILURE = 'failure';
    const ERROR = 'error';
    const MESSAGE_ID = 'message_id';

    /**
     * @var bool
     */
    protected $logEnabled = false;

    /**
     * The logger.
     *
     * @var \Monolog\Logger
     */
    protected $logger;

    /**
     * The value of the first Retry-After header in the response.
     *
     * @see https://httpwg.org/specs/rfc7231.html#header.retry-after
     * @var int|string|null
     */
    protected $retryAfter;

    /**
     * BaseResponse constructor.
     *
     * @param \Psr\Http\Message\ResponseInterface $response
     */
    public function __construct(ResponseInterface $response, Logger $logger)
    {
        $this->logger = $logger;
        $this->checkIsJsonResponse($response);
        $this->logEnabled = app('config')->get('fcm.log_enabled', false);
        $this->retryAfter = DownstreamResponse::getRetryAfterHeader($response);
        $responseInJson = json_decode($response->getBody(), true);
        $this->parseResponse($responseInJson);
    }

    /**
     * Check if the response given by fcm is parsable.
     *
     * @param \Psr\Http\Message\ResponseInterface $response
     *
     * @throws InvalidRequestException
     * @throws ServerResponseException
     * @throws UnauthorizedRequestException
     * @return void
     */
    private function checkIsJsonResponse(ResponseInterface $response)
    {
        if ($response->getStatusCode() == 200) {
            return;
        }

        if ($response->getStatusCode() == 400) {
            throw new InvalidRequestException($response);
        }

        if ($response->getStatusCode() == 401) {
            throw new UnauthorizedRequestException($response);
        }

        throw new ServerResponseException($response);
    }

    /**
     * @return int|string|null
     */
    public function getRetryAfterHeaderValue()
    {
        return $this->retryAfter;
    }

    /**
     * parse the response.
     *
     * @param array $responseInJson
     * @return void
     */
    abstract protected function parseResponse($responseInJson);

    /**
     * Log the response.
     *
     * @return void
     */
    abstract protected function logResponse();
}
