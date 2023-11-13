<?php

namespace App\Controller;

use App\Repository\FolderRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/')]
class HomePageController extends AbstractController
{
    #[Route('/', name: 'app_homepage')]
    public function index() 
    // Response
    {
        // FolderRepository $folderRepository
        // if (!$this->getUser()) {
        //     return $this->redirectToRoute('app_login');
        // }
        // $userId = $this->getUser()->getId();
        // $folders = $folderRepository->findByOwner($userId);

        // $folders = $folderRepository->findBy(['owner' => $userId]);

        return $this->render('homepage/homepage.html.twig',
            // 'filteredFolders' => $folders,
        );
        // return $this->render('home/homepage.html.twig',  [
        //     'folders' => $folderRepository->findAll(),
        // ]);
    }

    
}