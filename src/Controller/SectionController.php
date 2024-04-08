<?php

namespace App\Controller;

use App\Entity\Section;
use App\Entity\Formation;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\SectionType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\SectionRepository;
use App\Repository\RessourceRepository;
use App\Repository\FormationRepository;
use App\Service\Avancement;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

class SectionController extends AbstractController
{
    /**
     * @Route("/add/section", name="addsection")
     */
    public function addsection(Request $request)
    {
        $section = new Section();
        $form = $this->createForm(SectionType::class, $section);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $section = $form->getData();
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($section);
            $entityManager->flush();

            return $this->redirectToRoute('showformation');
        }
        return $this->render('section/index.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/edit/section/{id}", name="editsection")
     */
    public function editsection(Section $section, Request $request)
    {

        $form = $this->createForm(SectionType::class, $section);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $section = $form->getData();
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($section);
            $entityManager->flush();

            return $this->redirectToRoute('showformation');
        }
        return $this->render('section/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/delete/section/{id}", name="deletesection")
     */
    public function deletesection(Section $section, Request $request, RessourceRepository $ressourcerepository, FlashBagInterface $flashMessage)
    {
        // $idsection = $section->getId();
        // $ressourcesection = $ressourcerepository->findOneBy(['idsection' => $idsection]);
        // $entityManager = $this->getDoctrine()->getManager();
        // while ($ressourcesection) {
        //     $entityManager->remove($ressourcesection);
        //     $entityManager->flush();
        //     $ressourcesection = $ressourcerepository->findOneBy(['idsection' => $idsection]);
        // }
        // $entityManager->remove($section);
        // $entityManager->flush();
        // return $this->redirectToRoute('showformation');

        $entityManager = $this->getDoctrine()->getManager();
        $ressources = $ressourcerepository->findRessourcesBySectionId($section->getId());

        foreach ($ressources as $ressource) {
            $entityManager->remove($ressource);
        }

        $exercices = $section->getExercices();
        foreach ($exercices as $exercice) {
            $questions = $exercice->getQuestions();
            foreach ($questions as $question) {
                $choix = $question->getChoixReponses();
                foreach ($choix as $choice) {
                    $entityManager->remove($choice);
                }
                $entityManager->remove($question);
            }

            $entityManager->remove($exercice);
        }

        $entityManager->remove($section);

        $entityManager->flush();
        $flashMessage->add("success", "La section est bien supprimée !");

        return $this->redirectToRoute('showformation');
    }

    /**
     * @Route("/show/{idformation}/section", name="showsection")
     */
    public function showsection(SectionRepository $sectionrepository, $idformation, Avancement $avancement)
    {
        $section = $sectionrepository->findBy(['idformation' => $idformation]);

        $avancement_value = $avancement->GetUserAvancement($idformation, $this->getUser()->getId());

        return $this->render('section/showsection.html.twig', [
            'sections' => $section,
            'avancement' => $avancement_value
        ]);
    }
}
