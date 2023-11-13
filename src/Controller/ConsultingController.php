<?php

namespace App\Controller;

use Symfony\Component\Form\FormError;
use App\Entity\Consulting;
use App\Form\ConsultingType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ConsultingController extends AbstractController
{
    #[Route('/consulting/{slug}', name: 'app_consulting')]
    public function index(Request $request, Consulting $consulting): Response
    {
        $form = $this->createForm(ConsultingType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $codeSaisie = $form->get('code')->getData();

            if($codeSaisie == $consulting->getCode()){
                
                // recup nom du fichier
                $fichier = $consulting->getDocument()->getName();
                // convertion fichier en objet
                $file = new File($this->getParameter('upload_directory').'/'.$fichier);

                return $this->file($file, $fichier , ResponseHeaderBag::DISPOSITION_INLINE);
            } else {
                $this->addFlash('danger', 'code incorrecte');
                $form->get('code')->addError(new FormError('le code est incorrecte !'));
            }
        }
        return $this->render('consulting/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
