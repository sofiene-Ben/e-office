<?php

namespace App\Controller;

use App\Entity\Library;
use App\Form\LibraryType;
use App\Repository\LibraryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/library')]
class LibraryController extends AbstractController
{
    #[Route('/', name: 'app_library_index', methods: ['GET'])]
    public function index(LibraryRepository $libraryRepository): Response
    {
        return $this->render('library/index.html.twig', [
            'libraries' => $libraryRepository->findBy(['owner' => $this->getUser()]),
            
            
        ]);
    }

    #[Route('/new', name: 'app_library_new', methods: ['GET', 'POST'])]
    public function new(Request $request, LibraryRepository $libraryRepository): Response
    {
        $library = new Library();
        $form = $this->createForm(LibraryType::class, $library);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $library->setOwner($this->getUser());
            $libraryRepository->save($library, true);

            return $this->redirectToRoute('app_library_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('library/new.html.twig', [
            'library' => $library,
            'form' => $form,
        ]);
    }

    #[Route('/{slug}', name: 'app_library_show', methods: ['GET'])]
    public function show(Library $library): Response
    {
        return $this->render('library/show.html.twig', [
            'library' => $library,
        ]);
    }

    #[Route('/{slug}/edit', name: 'app_library_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Library $library, LibraryRepository $libraryRepository): Response
    {
        $form = $this->createForm(LibraryType::class, $library);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $libraryRepository->save($library, true);

            return $this->redirectToRoute('app_library_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('library/edit.html.twig', [
            'library' => $library,
            'form' => $form,
        ]);
    }

    #[Route('/{slug}', name: 'app_library_delete', methods: ['POST'])]
    public function delete(Request $request, Library $library, LibraryRepository $libraryRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$library->getId(), $request->request->get('_token'))) {
            $libraryRepository->remove($library, true);
        }

        return $this->redirectToRoute('app_library_index', [], Response::HTTP_SEE_OTHER);
    }
}
