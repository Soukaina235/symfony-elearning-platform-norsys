<?php

namespace App\Controller;

use App\Entity\Formation;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\SectionRepository;
use App\Repository\FormationRepository;
use App\Repository\RessourceRepository;
use Symfony\Component\HttpFoundation\Request;
use App\Form\FormationFormType;
use App\Repository\ExerciceRepository;
use App\Service\Avancement;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

class FormationController extends AbstractController
{
    /**
     * @Route("/formation/add", name="addformation")
     */
    public function addformation(Request $request, FlashBagInterface $flashMessage)
    {
        $formation = new Formation;
        $form = $this->createForm(FormationFormType::class, $formation);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $formation = $form->getData();
            //image upload
            $image = $form['image']->getData();
            if ($image) {
                $image_name = $image->getClientOriginalName();
                $image->move($this->getParameter("photo_directory"), $image_name);
                $formation->setImage($image_name);
            }
            //..........
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($formation);
            $entityManager->flush();

            $flashMessage->add("success", "La formation est bien ajoutée !");

            return $this->redirectToRoute('showformation');
        }

        return $this->render('formation/index.html.twig', [
            'form' => $form->createView()
        ]);
    }


    /**
     * @Route("/edit/formation/{id}", name="editformation")
     */
    public function editformation(Formation $formation, Request $request)
    {
        $formation->setImage(null);
        $form = $this->createForm(FormationFormType::class, $formation);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $formation = $form->getData();
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($formation);
            $entityManager->flush();

            return $this->redirectToRoute('showformation');
        }
        return $this->render('formation/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/delete/formation/{id}", name="deleteformation")
     */
    public function deleteformation($id, Request $request, SectionRepository $sectionrepository, RessourceRepository $ressourcerepository, FormationRepository $formationRepository, FlashBagInterface $flashMessage)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $formation = $formationRepository->find($id);
        $sections = $sectionrepository->findSectionByFormationId($id);

        foreach ($sections as $section) {
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
        }

        $entityManager->remove($formation);
        $entityManager->flush();
        $flashMessage->add("success", "La formation est bien supprimée !");

        return $this->redirectToRoute('showformation');
    }


    /**
     * @Route("/show/formation", name="showformation")
     */

    public function showformation(FormationRepository $formationrepository, Avancement $avancement)
    {
        $formation = $formationrepository->findAll();

        $avancement_values = [];
        foreach ($formation as $uneformation) {
            array_push($avancement_values, $avancement->GetUserAvancement($uneformation->getId(), $this->getUser()->getId()));
        }
        return $this->render('formation/showformation.html.twig', [
            'formation' => $formation,
            'avancement_values' => $avancement_values
        ]);
    }

    /**
     * @Route("/search", name="search_form")
     */
    public function searchForm(Request $request, Avancement $avancement)
    {
        $query = $request->query->get('query');

        $formations = $this->getDoctrine()->getRepository(Formation::class)->findByTitre($query);

        $avancement_values = [];
        foreach ($formations as $formation) {
            array_push($avancement_values, $avancement->GetUserAvancement($formation->getId(), $this->getUser()->getId()));
        }

        return $this->render('formation/showformation.html.twig', [
            'formation' => $formations,
            'query' => $query,
            'avancement_values' => $avancement_values
        ]);
    }
}
