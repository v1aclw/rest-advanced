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
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\Uuid;

#[Route('/api/v1/login', name: 'api_login', methods: ['POST'])]
class LoginAction extends AbstractController
{
    public function __invoke(
        UserRepository $userRepository,
        EntityManagerInterface $entityManager,
        TokenStorage $tokenStorage,
        Request $request
    ): Response {
        if ((null === $login = $request->get('login')) || false === is_scalar($login) || '' === $login = (string) $login) {
            throw new BadRequestHttpException('Missing credentials');
        }

        if (null === $user = $userRepository->findOneBy(['login' => $login])) {
            $user = new User((string) Uuid::v4(), $login);
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
