<?php

declare(strict_types=1);

namespace App\Controller\Api\Document;

use App\Repository\DocumentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;

#[Route('/api/v1/document/{id}/publish', name: 'api_document_publish', methods: ['POST'], requirements: ['id' => Requirement::UUID_V4])]
class PublishDocumentAction extends AbstractController
{
    public function __invoke(DocumentRepository $documentRepository, EntityManagerInterface $entityManager, string $id): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        if (null === $document = $documentRepository->find($id)) {
            throw $this->createNotFoundException();
        }

        if (false === $document->isOwner($this->getUser())) {
            throw $this->createAccessDeniedException();
        }

        if (false === $document->isPublised()) {
            $document->publish();
            $entityManager->flush();
        }

        return $this->json(['document' => $document]);
    }
}
