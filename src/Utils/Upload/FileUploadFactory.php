<?php

namespace App\Utils\Upload;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class FileUploadFactory
{
    /**
     * @param String $fileType
     * @param UploadedFile $file
     * @param EntityManagerInterface $entityManager
     * @param UserPasswordHasherInterface $passwordHasher
     * @return BaseFileUpload
     */
    public static function getFileUploadInstance
    (
        String $fileType,
        UploadedFile $file,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ): BaseFileUpload
    {
        switch ($fileType) {
            case 'csv':
                return new CsvUpload($file, $entityManager, $passwordHasher);
            default:
                throw new FileException("Invalid file type");
        }
    }
}