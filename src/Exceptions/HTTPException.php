<?php

namespace App\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

abstract class HTTPException extends \Exception implements HttpExceptionInterface, ReadableCodeHTTPExceptionInterface
{
    abstract public function getStatusCode(): int;
    abstract public function getErrorCode(): string;

    public function getHeaders()
    {
        return ['Content-Type' => 'application/json'];
    }
}