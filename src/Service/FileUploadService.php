<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileUploadService extends AbstractService
{
    CONST AUTHORIZED_MIME_TYPES = [
        'text/plain',
        'text/html',
        'application/octet-stream',
        'application/vnd.ms-excel',
        'application/x-csv',
        'application/csv',
        'application/excel',
        'application/vnd.msexcel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
    ];

    private $entityManager;
    private $targetDirectory;
    private $slugger;

    public function __construct(
        $targetDirectory,
        EntityManagerInterface $entityManager,
        SluggerInterface $slugger
    ) {
        $this->entityManager = $entityManager;
        $this->targetDirectory = $targetDirectory;
        $this->slugger = $slugger;
    }

    public function uploadFile(UploadedFile $uploadedFile): string
    {
        $extension = $uploadedFile->getClientOriginalExtension();

        if (!in_array($uploadedFile->getMimeType(), self::AUTHORIZED_MIME_TYPES)) {
            return '';
        }

        if (empty($extension)) {
            $extension = self::DEFAULT_EXTENSION;
        }

        $originalFilename   = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename       = $this->slugger->slug($originalFilename);
        $fileName           = $safeFilename.'-'.uniqid().'.'.$extension;

        try {
            $uploadedFile->move($this->getTargetDirectory(), $fileName);
        } catch (FileException $e) {
            return new UploadFileException($e->getMessage());
        }

        return $fileName;
    }

    public function getTargetDirectory(): string
    {
        return $this->targetDirectory;
    }
}