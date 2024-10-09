<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Token\TokenStorage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\Uuid;

class ApiLoginController extends AbstractController
{
    #[Route('/api/v1/login', name: 'api_login', methods: ['POST'])]
    public function index(UserRepository $userRepository, EntityManagerInterface $entityManager, TokenStorage $tokenStorage, Request $request): Response
    {
        
        if (null === $login = $request->get('login')) {
            return $this->json([
                'message' => 'missing credentials',
            ], Response::HTTP_UNAUTHORIZED);
        }

        if (null === $user = $userRepository->findOneBy(['login' => $login])) {
            $user = new User((string)Uuid::v4(), $login);
            $entityManager->persist($user);
            $entityManager->flush();
        }

        $tokenStorage->set($token = md5(random_bytes(32)), $user->getId());

        return $this->json([
            'user' => $user->getLogin(),
            'token' => $token,
            'until' => time() + 3600
        ]);
    }
}
