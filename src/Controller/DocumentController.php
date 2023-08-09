<?php

namespace App\Controller;

// use App\Entity\User;
use App\Entity\Folder;
use App\Entity\Document;
use App\Form\DocumentFormType;
use App\Form\DocumentShareType;
use App\Repository\DocumentRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

#[Route('/{slug_folder}/document')]
#[Entity('folder', expr: 'repository.findOneBySlug(slug_folder)')]
class DocumentController extends AbstractController
{
    #[Route('/', name: 'app_document_index', methods: ['GET'])]
    public function index(Folder $folder, DocumentRepository $documentRepository): Response
    {
        return $this->render('document/index.html.twig', [
            'documents' => $documentRepository->findBy(['folder' => $folder]),
            'folder' => $folder,
        ]);
    }


    #[Route('/new', name: 'app_document_new', methods: ['GET', 'POST'])]
    public function new(Folder $folder, Request $request, SluggerInterface $slugger, DocumentRepository $documentRepository): Response
    {

        $document = new Document();
        $form = $this->createForm(DocumentFormType::class, $document);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $document->setOwner($this->getUser());
            $document->setFolder($folder);
            
            /** @var UploadedFile $file */
            $file = $form->get('file')->getData();

            if ($file) {

                $originalFileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

                $safeFileName = $slugger->slug($originalFileName);
                $newFileName = $safeFileName .'-'.uniqid().'.'.$file->guessExtension();


                // deplacer le fichier dans le dossier de stockage

                try {
                    $file->move(
                        $this->getParameter('upload_directory'),
                        $newFileName
                    );
                } catch (FileException $e){
                    
                }

                $document->setName($newFileName);
            }

            $documentRepository->save($document, true);
            return $this->redirectToRoute('app_document_index', ['slug_folder' => $folder->getSlug()]);
  
        }

        return $this->renderForm('document/new.html.twig', [
            'form' => $form,
            'document' => $document,
            'folder' => $folder,
        ]);
    }

    #[Route('/{slug}/show', name: 'app_document_show', methods: ['GET', 'POST'])]
    #[Entity('document', expr: 'repository.findOneBySlug(slug)')]
    public function show(Folder $folder, Document $document, Request $request, MailerInterface $mailer): Response
    {
        $username = "test@live.fr";

        $form = $this->createForm(DocumentShareType::class);
        $contact = $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $email = (new TemplatedEmail())
                ->from($username)
                ->to($contact->get('email')->getData())
                ->subject('Vous venez de recevoir un nouveau document')
                ->htmlTemplate('emails/share_document.html.twig')
                ->context([
                    'document' => $document,
                    'mail' => $username,
                    'message' => $contact->get('message')->getData()
                ]);
            $mailer->send($email);

            $this->addFlash('message', 'votre document a bien été envoyer');
            return $this->redirectToRoute('app_library_index');
        }

        return $this->render('document/show.html.twig', [
            'document' => $document,
            'folder' => $folder,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{slug}/edit', name: 'app_document_edit', methods: ['GET', 'POST'])]
    #[Entity('document', expr: 'repository.findOneBySlug(slug)')]
    public function edit(Folder $folder, Request $request, SluggerInterface $slugger, Document $document, DocumentRepository $documentRepository): Response
    {
        $oldDocumentName = $document->getName();
        $form = $this->createForm(DocumentFormType::class, $document);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var UploadedFile $file */
            $file = $form->get('file')->getData();

            if ($file) {

                $originalFileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

                $safeFileName = $slugger->slug($originalFileName);
                $newFileName = $safeFileName .'-'.uniqid().'.'.$file->guessExtension();


                // deplacer le fichier dans le dossier de stockage

                try {
                    $file->move(
                        $this->getParameter('upload_directory'),
                        $newFileName
                    );
                } catch (FileException $e){
                    
                }

                $document->setName($newFileName);
            }

            $documentRepository->save($document, true);

            // supp du file dans le dossier upload
            $filePath = $this->getParameter('upload_directory').'/'.$oldDocumentName;
            // Vérifier si le fichier existe avant de le supprimer
            if (file_exists($filePath)) {
                // Supprimer le fichier
                unlink($filePath);
            }

            return $this->redirectToRoute('app_document_index', ['slug_folder' => $folder->getSlug()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('document/edit.html.twig', [
            'document' => $document,
            'form' => $form,
            'folder' => $folder,
        ]);
    }

    #[Route('/{slug}/delete', name: 'app_document_delete', methods: ['POST'])]
    #[Entity('document', expr: 'repository.findOneBySlug(slug)')]
    public function delete(Folder $folder, Request $request, Document $document, DocumentRepository $documentRepository): Response
    {
        $oldDocumentName = $document->getName();

        if ($this->isCsrfTokenValid('delete'.$document->getId(), $request->request->get('_token'))) {
            $documentRepository->remove($document, true);

            // supp du file dans le dossier upload
            $filePath = $this->getParameter('upload_directory').'/'.$oldDocumentName;
            // Vérifier si le fichier existe avant de le supprimer
            if (file_exists($filePath)) {
                // Supprimer le fichier
                unlink($filePath);
            }
            
        }

        return $this->redirectToRoute('app_document_index', ['slug_folder' => $folder->getSlug()], Response::HTTP_SEE_OTHER);
    }
}