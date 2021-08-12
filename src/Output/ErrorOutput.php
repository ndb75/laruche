<?php

namespace App\Output;

use Symfony\Component\Serializer\Annotation\Groups;

class ErrorOutput
{
    /**
     * @var int
     * @Groups({
     *     "api.error"
     * })
     */
    public $errorCode;

    /**
     * @var string[]
     * @Groups({
     *     "api.error"
     * })
     */
    public $errors;

    public function __construct($errors, $errorCode)
    {
        $this->errors = $errors;
        $this->errorCode = $errorCode;
    }
}
