<?php

namespace App\Controller;

use App\Entity\Folder;
use App\Entity\Library;
use App\Form\FolderType;
use App\Repository\FolderRepository;
use App\Repository\DocumentRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/{id}/folder')]
class FolderController extends AbstractController
{

    private $documentRepository;
    public function __construct(DocumentRepository $documentRepository)
    {
        $this->documentRepository = $documentRepository;
    }
    
    #[Route('/', name: 'app_folder_index', methods: ['GET'])]
    public function index(Library $library, FolderRepository $folderRepository): Response
    {
        return $this->render('folder/index.html.twig', [
            'folders' => $folderRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_folder_new', methods: ['GET', 'POST'])]
    public function new(Library $library, Request $request, FolderRepository $folderRepository): Response
    {
        $folder = new Folder();
        $form = $this->createForm(FolderType::class, $folder);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $folder->setOwner($this->getUser());
            $folder->setLibrary($library);
            $folderRepository->save($folder, true);

            return $this->redirectToRoute('app_library_show', ['slug' => $library->getSlug()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('folder/new.html.twig', [
            'folder' => $folder,
            'library' => $library,
            'form' => $form,
        ]);
    }

    #[Route('/{slug}', name: 'app_folder_show', methods: ['GET'])]
    public function show(Library $library, Folder $folder): Response
    {
        $documents = $this->documentRepository->findBy(['folder' => $folder]);

        return $this->render('folder/show.html.twig', [
            'folder' => $folder,
            'library' => $library,
            'documents' => $documents,
        ]);
    }

    #[Route('/{slug}/edit', name: 'app_folder_edit', methods: ['GET', 'POST'])]
    public function edit(Library $library, Request $request, Folder $folder, FolderRepository $folderRepository): Response
    {
        $form = $this->createForm(FolderType::class, $folder);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $folderRepository->save($folder, true);

            return $this->redirectToRoute('app_library_show', ['slug' => $library->getSlug()], Response::HTTP_SEE_OTHER);

        }

        return $this->renderForm('folder/edit.html.twig', [
            'folder' => $folder,
            'library' => $library,
            'form' => $form,
        ]);
    }

    #[Route('/{slug}', name: 'app_folder_delete', methods: ['POST'])]
    public function delete(Library $library, Request $request, Folder $folder, FolderRepository $folderRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$folder->getId(), $request->request->get('_token'))) {
            $folderRepository->remove($folder, true);
        }

        return $this->redirectToRoute('app_folder_index', [], Response::HTTP_SEE_OTHER);
    }
}
