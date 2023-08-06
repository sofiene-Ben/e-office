<?php

namespace App\Controller;

use App\Entity\Folder;
use App\Entity\Library;
use App\Form\FolderType;
use App\Form\SearchDataType;
use App\Repository\FolderRepository;
use App\Repository\DocumentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/{slug_lib}/folder')]
#[Entity('library', expr: 'repository.findOneBySlug(slug_lib)')]
class FolderController extends AbstractController
{

    private $documentRepository;
    public function __construct(DocumentRepository $documentRepository)
    {
        $this->documentRepository = $documentRepository;
    }
    
    #[Route('/', name: 'app_folder_index', methods: ['GET', 'POST'])]
    public function index(Library $library, FolderRepository $folderRepository, Request $request): Response
    {
        $folders = $folderRepository->findBy(['library' => $library]);

        $form = $this->createForm(SearchDataType::class);
        $search = $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()){
            // on recherche les dossier correspondant aux mots clés
            $folders = $folderRepository->findBySearch($search->get('word')->getData(), $library);
        }
        return $this->render('folder/index.html.twig', [
            'folders' => $folders,
            'library' => $library,
            'form' => $form->createView(),
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

    #[Route('/{slug}/show', name: 'app_folder_show', methods: ['GET', 'POST'])]
    #[Entity('folder', expr: 'repository.findOneBySlug(slug)')]
    public function show(Library $library, Folder $folder, DocumentRepository $documentRepository, Request $request): Response
    {

        $documents = $this->documentRepository->findBy(['folder' => $folder]);

        $form = $this->createForm(SearchDataType::class);
        $search = $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()){
            // on recherche les dossier correspondant aux mots clés
            $documents = $documentRepository->findBySearch($search->get('word')->getData(), $folder);
        }

        return $this->render('folder/show.html.twig', [
            'folder' => $folder,
            'library' => $library,
            'documents' => $documents,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{slug}/edit', name: 'app_folder_edit', methods: ['GET', 'POST'])]
    #[Entity('folder', expr: 'repository.findOneBySlug(slug)')]
    public function edit(Library $library, Folder $folder, Request $request): Response
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

    #[Route('/{slug}/delete', name: 'app_folder_delete', methods: ['POST'])]
    #[Entity('folder', expr: 'repository.findOneBySlug(slug)')]
    public function delete(Library $library, Folder $folder, Request $request, FolderRepository $folderRepository, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete'.$folder->getId(), $request->request->get('_token'))) {
            $documents = $folder->getDocuments();
            foreach($documents as $document) {
                $oldDocumentName = $document->getName();
                $document->setFolder(null);

                //supp de doc en bdd
                $em->remove($document);

                // supp du file dans le dossier upload
                $filePath = $this->getParameter('upload_directory').'/'.$oldDocumentName;
                // Vérifier si le fichier existe avant de le supprimer
                if (file_exists($filePath)) {
                    // Supprimer le fichier
                    unlink($filePath);
                }
            }
            $folderRepository->remove($folder, true);
        }

        return $this->redirectToRoute('app_library_show', ['slug' => $library->getSlug()], Response::HTTP_SEE_OTHER);
    }
}
