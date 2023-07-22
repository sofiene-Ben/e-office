<?php

namespace App\Controller;

use App\Entity\Document;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\File;

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

    #[Route('/{slug}/download', name: 'document_download')]
    public function download(Request $request, Document $document): BinaryFileResponse
    {
        $file = new File($this->getParameter('upload_directory').'/'.$document->getName());

        return $this->file($file);
    }
}
