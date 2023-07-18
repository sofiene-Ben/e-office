<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PDFController extends AbstractController
{
    #[Route('/uploads/documents/{filename}', name: 'pdf_show')]
    public function show(string $filename): Response
    {
        $path = $this->getParameter('upload_directory') . '/' . $filename; // Chemin vers le fichier PDF

        // Créer la réponse avec le fichier PDF
        $response = new Response();
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Disposition', 'inline; filename="' . $filename . '"');
        $response->setContent(file_get_contents($path));

        return $response;
    }
}
