<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\Document;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\Uuid;

#[Route('/api/v1/document', name: 'api_document_create', methods: ['POST'])]
class CreateDocumentAction extends AbstractController
{
    public function __invoke(EntityManagerInterface $entityManager, ): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $entityManager->persist($document = new Document((string)Uuid::v4(), $this->getUser()));
        $entityManager->flush();

        return $this->json(['document' => $document]);
    }
}
