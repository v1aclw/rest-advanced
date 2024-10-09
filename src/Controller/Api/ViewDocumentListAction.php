<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Repository\DocumentRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/v1/document', name: 'api_document_view_list', methods: ['GET'])]
class ViewDocumentListAction extends AbstractController
{
    public function __invoke(DocumentRepository $documentRepository, Request $request): Response
    {
        $query = $documentRepository->getListQuery($this->getUser());
        $page = (int)$request->get('page', 1);
        $page = $page < 1 ? 1 : $page;
        $perPage = 20;
        $paginator = new Paginator($query);
        $total = count($paginator);
        $paginator
            ->getQuery()
            ->setFirstResult((int)($page - 1) * $perPage)
            ->setMaxResults($perPage);

        return $this->json(
            [
                'document' => $paginator,
                'pagination' => [
                    'page' => $page,
                    'perPage' => $perPage,
                    'total' => $total
                ]
            ]
        );
    }
}
