<?php

namespace App\Utils\Upload;


use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

abstract class BaseFileUpload
{
    protected $file;
    protected EntityManagerInterface $entityManager;
    protected UserPasswordHasherInterface $passwordHasher;

    /**
     * @param UploadedFile $file
     * @param EntityManagerInterface $entityManager
     * @param UserPasswordHasherInterface $passwordHasher
     */
    public function __construct
    (
        UploadedFile $file,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    )
    {
        $this->file = $file;
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
    }

    /**
     * Parse uploaded file and save the contents to the DB
     * @return string|null
     */
    abstract public function upload():?string;
}