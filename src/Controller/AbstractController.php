<?php

namespace App\Controller;

use App\Exceptions\InvalidEntityException;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as SymfonyController;

abstract class AbstractController extends SymfonyController {

    public function __construct() {

    }

    protected function deserialize($request, $class)
    {
        try {
            return $this->serializer->deserialize($request->getContent(), $class, 'json');
        } catch (NotNormalizableValueException $e) {
            throw new InvalidEntityException($e->getMessage());
        }
    }
}