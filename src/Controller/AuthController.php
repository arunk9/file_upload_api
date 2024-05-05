<?php

// src/Controller/AuthController.php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;

class AuthController extends AbstractController
{
    private $jwtManager;
    private $passwordEncoder;

    public function __construct(JWTTokenManagerInterface $jwtManager, UserPasswordHasherInterface $passwordEncoder)
    {
        $this->jwtManager = $jwtManager;
        $this->passwordEncoder = $passwordEncoder;
    }

    #[Route('/api/login', name: 'app_login')]
    public function login(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Retrieve credentials from the request
        $data = json_decode($request->getContent(), true);
        $username = $data['username'];
        $password = $data['password'];

        // Authenticate user (fetch user from database)
        $user = $entityManager->getRepository(User::class)->findOneBy(['username' => $username]); // Fetch user from database based on username/email

        if (!$user || !$this->passwordEncoder->isPasswordValid($user, $password)) {
            return $this->json([
                'error' => [
                    'message' => 'Invalid credentials']
                ]
                , Response::HTTP_UNAUTHORIZED
            );
        }

        // Generate JWT token
        $token = $this->jwtManager->create($user);

        // Return token in response
        return $this->json([
            'data' => [
                'token' => $token
            ]
        ]);
    }

    /**
     * @Route("/api/logout", name="app_logout", methods={"POST"})
     */
    public function logout()
    {
        // This controller is empty because it will be intercepted by the security system
    }
}

