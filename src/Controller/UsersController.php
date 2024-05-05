<?php

namespace App\Controller;

use App\Entity\User;
use App\Utils\Upload\FileUploadFactory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class UsersController extends AbstractController
{
    #[Route('/api/users_import', name: 'file_upload', methods: ['POST'])]
    public function import(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        // Handle file upload
        $uploadedFile = $request->files->get('csv_file');

        if (!$uploadedFile) {
            return $this->json([
                    'error' => [
                        'message' => 'No file uploaded']
                ]
                , Response::HTTP_BAD_REQUEST
            );
        }

        try {
            // get file upload util
            $fileUploadUtil = FileUploadFactory::getFileUploadInstance(
                $uploadedFile->getClientOriginalExtension(),
                $uploadedFile,
                $entityManager,
                $passwordHasher
            );
            $batchId = $fileUploadUtil->upload();

            return $this
                        ->json([
                                'data' => [
                                    'message' => 'File uploaded successfully',
                                    'batch_id' => $batchId,
                                ]
                            ],
                            Response::HTTP_OK
                        );

        } catch (\Exception $e) {
            return $this->json([
                    'error' => [
                        'message' => $e->getMessage()
                    ]
                ]
                , Response::HTTP_BAD_REQUEST
            );
        }
    }



    #[Route('/api/users/{batchId}', name: 'list_users', methods: ['GET'])]
    public function getUsers(string $batchId, Request $request, EntityManagerInterface $entityManager): Response
    {
        $users = $entityManager
                    ->getRepository(User::class)
                    ->getUsers(
                        $batchId,
                        $request->get('search_text', ''),
                        $request->get('sort_key', 'username'),
                        $request->get('sort_order', 'ASC')
                    );

        return $this->json(['data' => $users], Response::HTTP_OK);
    }
}