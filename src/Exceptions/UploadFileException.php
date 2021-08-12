<?php

namespace App\Exceptions;

use Symfony\Component\HttpFoundation\Response;

class UploadFileException extends HTTPException
{
    public function getStatusCode(): int
    {
        return Response::HTTP_CONFLICT ;
    }

    public function getErrorCode(): string
    {
        return 'upload_file_error';
    }
}
