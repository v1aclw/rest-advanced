<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/', name: 'index', methods: ['GET'])]
class IndexAction extends AbstractController {
    public function __invoke()
    {
        return $this->render('index.html.twig');
    }
}