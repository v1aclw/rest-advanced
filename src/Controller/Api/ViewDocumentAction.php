<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\Document;
use App\Repository\DocumentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;

#[Route('/api/v1/document/{id}', name: 'api_document_view', methods: ['GET'], requirements: ['id' => Requirement::UUID_V4])]
class ViewDocumentAction extends AbstractController
{
    public function __invoke(DocumentRepository $documentRepository, string $id): Response
    {
        if (null === $document = $documentRepository->find($id)) {
            throw $this->createNotFoundException();
        }


        if (true === $document->isPublised() || true === $document->isOwner($this->getUser())) {
            return $this->json(['document' => $document]);
        }

        throw $this->createAccessDeniedException();
    }
}
