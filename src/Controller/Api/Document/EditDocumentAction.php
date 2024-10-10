<?php

declare(strict_types=1);

namespace App\Controller\Api\Document;

use App\Repository\DocumentRepository;
use App\Json\JsonPatch;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;

#[Route('/api/v1/document/{id}', name: 'api_document_edit', methods: ['PATCH'], requirements: ['id' => Requirement::UUID_V4])]
class EditDocumentAction extends AbstractController
{
    public function __construct(private JsonPatch $jsonPatch)
    {
    }

    public function __invoke(DocumentRepository $documentRepository, EntityManagerInterface $entityManager, Request $request, string $id): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        if (null === $payload = $this->getPayloadFromRequest($request)) {
            throw new BadRequestHttpException('Payload is not defined');
        }

        if (null === $document = $documentRepository->find($id)) {
            throw $this->createNotFoundException();
        }

        if (false === $document->isOwner($this->getUser())) {
            throw $this->createAccessDeniedException();
        }

        if (true === $document->isPublised()) {
            throw new BadRequestHttpException('Already published');
        }

        $document->editPayload($this->jsonPatch, $payload);
        $entityManager->flush();

        return $this->json(['document' => $document]);
    }

    private function getPayloadFromRequest(Request $request): null|array
    {
        if (null === $document = $request->get('document')) {
            return null;
        }

        if (false === array_key_exists('payload', $document)) {
            return null;
        }

        if (false === is_array($document['payload'])) {
            return null;
        }

        if (false === $this->jsonPatch->isObject($document['payload'])) {
            return null;
        }

        return $document['payload'];
    }
}
