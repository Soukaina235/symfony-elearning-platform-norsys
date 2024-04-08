<?php

namespace App\Controller;

use App\Entity\Ressource;
use App\Entity\RessourceUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\RessourceType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\RessourceRepository;
use App\Repository\ExerciceRepository;
use App\Repository\FormationRepository;
use App\Repository\RessourceUserRepository;
use App\Repository\SectionRepository;
use App\Service\Avancement;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

class RessourceController extends AbstractController
{
    private $flashMessage;
    private $exerciceRepository;

    public function __construct(ExerciceRepository $exerciceRepository, FlashBagInterface $flashMessage)
    {
        $this->exerciceRepository = $exerciceRepository;
        $this->flashMessage = $flashMessage;
    }

    /**
     * @Route("/home", name="home")
     */
    public function home()

    {
        return $this->render('home.html.twig');
    }

    /**
     * @Route("/add/ressource", name="addressource")
     */
    public function addressource(Request $request, FlashBagInterface $flashMessage)

    {
        $ressource = new Ressource();
        $form = $this->createForm(RessourceType::class, $ressource);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $ressource = $form->getData();
            //image upload
            $lien = $form['lien']->getData();
            if ($lien) {
                $lien_name = $lien->getClientOriginalName();
                $lien->move($this->getParameter("photo_directory"), $lien_name);
                $ressource->setLien($lien_name);
            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($ressource);
            $entityManager->flush();

            $flashMessage->add("success", "La ressource est bien ajoutée !");

            // You can add a flash message here to notify the user about successful registration.
            // $this->addFlash('success', 'Registration successful!');

            // return $this->redirectToRoute('showformation');
            return $this->redirectToRoute(
                'showressource',
                ['idsection' => $ressource->getIdsection()]
            );
        }
        return $this->render('ressource/index.html.twig', [
            'form' => $form->createView()
        ]);
    }




    /**
     * @Route("/edit/{idsection}/ressource/{id}", name="editressource")
     */
    public function editressource(Ressource $ressource, Request $request, FlashBagInterface $flashMessage, $idsection)
    {
        $ressource->setLien(null);
        $form = $this->createForm(RessourceType::class, $ressource);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $ressource = $form->getData();
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($ressource);
            $entityManager->flush();

            $flashMessage->add("success", "La ressource est bien modifiée !");
            return $this->redirectToRoute(
                'showressource',
                ['idsection' => $idsection]
            );

            // return $this->redirectToRoute('showformation');
        }
        return $this->render('ressource/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }



    /**
     * @Route("/delete/{idsection}/ressource/{id}", name="deleteressource")
     */
    public function deleteressource(Ressource $ressource, Request $request, RessourceRepository $ressourcerepository, $id, $idsection, FlashBagInterface $flashMessage)
    {
        $ressource = $ressourcerepository->findOneBy(['id' => $id]);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($ressource);
        $entityManager->flush();

        $flashMessage->add("success", "La ressource est bien supprimée !");

        return $this->redirectToRoute(
            'showressource',
            ['idsection' => $idsection]
        );
    }



    /**
     * @Route("/show/{idsection}/ressource", name="showressource")
     */

    public function showressource(RessourceRepository $ressourceRepository, ExerciceRepository $exerciceRepository, $idsection, Avancement $avancement, SectionRepository $sectionRepository, FormationRepository $formationRepository)
    {
        $section = $sectionRepository->find($idsection);
        $avancement_value = $avancement->GetUserAvancement($formationRepository->find($section->getIdformation()), $this->getUser()->getId());

        $ressource = $ressourceRepository->findBy(['idsection' => $idsection]);
        $exercices = $this->exerciceRepository->findBy(['section' => $idsection]);
        return $this->render('ressource/showressource.html.twig', [
            'ressources' => $ressource,
            "exercices" => $exercices,
            'avancement' => $avancement_value
        ]);
    }

    /**
     * @Route("/download/{id}/doc", name="download_doc")
     */
    public function downloaddoc(RessourceRepository $ressourceRepository, $id)
    {

        $ressource = $ressourceRepository->find($id);

        if (!$ressource) {
            throw $this->createNotFoundException('Ressource non trouvéed');
        }

        $ressourceTitre = $ressource->getTitre();
        $ressourceType = $ressource->getType();
        $ressourceLien = $ressource->getLien();
        // $filePath = $this->getParameter("photo_directory") . "/$ressourceLien";
        $filePath = $this->getParameter("photo_directory") . "\\$ressourceLien"; // Adjust the path accordingly

        echo $filePath;

        // if (!file_exists($filePath)) {
        //     throw $this->createNotFoundException('Fichier non trouvé');
        // }

        $response = new Response(file_get_contents($filePath));
        $response->headers->set('Content-Type', $ressourceType);
        $response->headers->set('Content-Disposition', "attachment; filename=$ressourceTitre");
        return $response;
    }

    /**
     * @Route("{idsection}/done/ressource/{ressource_id}", name="doneressource")
     */

    public function doneressource($ressource_id, Avancement $avancement, SectionRepository $sectionRepository, FormationRepository $formationRepository, RessourceRepository $ressourceRepository, ExerciceRepository $exerciceRepository, RessourceUserRepository $ressourceUserRepository, $idsection)
    {
        $exercice_user = $ressourceUserRepository->findOneByRessourceAndUser($ressource_id,  $this->getUser()->getId());

        $ressource = $ressourceRepository->findBy(['idsection' => $idsection]);
        $exercices = $exerciceRepository->findBy(['section' => $idsection]);

        $section = $sectionRepository->find($idsection);

        if ($exercice_user) {
            $avancement_value = $avancement->GetUserAvancement($formationRepository->find($section->getIdformation()), $this->getUser()->getId());
            $this->flashMessage->add("success", "La ressource est déjà marquée comme terminée !");
            return $this->render('ressource/showressource.html.twig', [
                'ressources' => $ressource,
                "exercices" => $exercices,
                'avancement' => $avancement_value
            ]);
        }

        $ressource_user = new RessourceUser();
        $ressource_user->setRessourceid($ressource_id);
        $ressource_user->setUserid($this->getUser()->getId());
        // dd($ressource_user);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($ressource_user);
        $entityManager->flush();
        // $this->showressource(new RessourceRepository(), new ExerciceRepository(), $idsection);
        $this->flashMessage->add("success", "Ressource marquée comme terminée !");

        $avancement_value = $avancement->GetUserAvancement($formationRepository->find($section->getIdformation()), $this->getUser()->getId());
        // dd($avancement_value);
        $avancement->updateUserAvancement($formationRepository->find($section->getIdformation())->getId(), $this->getUser()->getId(), $avancement_value);


        return $this->render('ressource/showressource.html.twig', [
            'ressources' => $ressource,
            "exercices" => $exercices,
            'avancement' => $avancement_value
        ]);
    }
}
