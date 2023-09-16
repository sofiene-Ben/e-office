<?php

namespace App\Controller;

use App\Repository\FolderRepository;
use App\Repository\LibraryRepository;
use App\Repository\DocumentRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SearchController extends AbstractController
{
    #[Route('/search', name: 'app_search')]
    public function index(Request $request, DocumentRepository $documentRepository, FolderRepository $folderRepository, LibraryRepository $libraryRepository): Response
    {
        $term = $request->query->get('q');
        $documents = [];
        $folders = [];
        $libraries = [];

        if($term){
            $user = $this->getUser();
            $documents = $documentRepository->findBySearch($term, $user);
            $folders = $folderRepository->findBySearch($term, $user);
            $libraries = $libraryRepository->findBySearch($term, $user);
        }
        //  else {
            // throw $this->createNotFoundException('Pas de resultat disponible');
        // }
        return $this->render('search/index.html.twig', [
            'documents' => $documents,
            'folders' => $folders,
            'libraries' => $libraries,
        ]);
    }
}
