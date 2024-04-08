<?php

namespace App\Controller;

use App\Entity\Exercice;
use App\Entity\ExerciceUser;
use App\Form\ExerciceType;
use App\Form\PracticeType;
use App\Repository\ExerciceRepository;
use App\Repository\ExerciceUserRepository;
use App\Repository\FormationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\Avancement;
use Doctrine\ORM\EntityManagerInterface;

class ExerciceController extends AbstractController
{
    private $exerciceRepository;
    private $flashMessage;
    private $entityManager;

    public function __construct(ExerciceRepository $exerciceRepository, FlashBagInterface $flashMessage, EntityManagerInterface $entityManager)
    {
        $this->exerciceRepository = $exerciceRepository;
        $this->flashMessage = $flashMessage;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/exercice", name="app_exercice")
     */
    public function index()
    {
        $exercices = $this->exerciceRepository->findAll();
        return $this->render('exercice/index.html.twig', [
            "exercices" => $exercices,
        ]);
    }

    /**
     * @Route("/exercice/show/{id}", name="app_exercice.show")
     */
    public function showExercice($id)
    {
        $exercice = $this->exerciceRepository->find($id);
        if (!$exercice) {
            throw $this->createNotFoundException('Aucun exercice trouvé pour l\'id ' . $id);
        }

        return $this->render('exercice/show.html.twig', [
            "exercice" => $exercice,
        ]);
    }

    /**
     * @Route("/exercice/add", name="app_exercice.add")
     */
    public function addExercice(Request $request)
    {
        $exercice = new Exercice();
        $form = $this->createForm(ExerciceType::class, $exercice);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $exercice = $form->getData();

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($exercice);
            $entityManager->flush();
            $this->flashMessage->add("success", "Exercise ajouté, maintenant ajoutez des questions !");

            return $this->redirectToRoute('app_exercice');
        }

        return $this->render('exercice/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/exercice/edit/{id}", name="app_exercice.edit")
     */
    public function editExercice(Exercice $exercice, Request $request)
    {
        $form = $this->createForm(ExerciceType::class, $exercice);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $exercice = $form->getData();
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($exercice);
            $entityManager->flush();
            $this->flashMessage->add("success", "L'exercice est bien modifié !");

            return $this->redirectToRoute('app_exercice');
        }

        return $this->render('exercice/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/exercice/delete/{id}", name="app_exercice.delete")
     */
    public function deleteExercice(Exercice $exercice)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $questions = $exercice->getQuestions();

        foreach ($questions as $question) {
            $choix = $question->getChoixReponses();
            foreach ($choix as $choice) {
                $entityManager->remove($choice);
            }
            $entityManager->remove($question);
        }

        $entityManager->remove($exercice);
        $entityManager->flush();
        $this->flashMessage->add("success", "L'exercice est bien supprimé !");

        return $this->redirectToRoute('app_exercice');
    }

    /**
     * @Route("/exercice/practice/{exercice_id}/{flag?0}", name="app_exercice.practice")
     */
    public function PracticeExercice(Request $request, SessionInterface $session, FormationRepository $formationRepository, ExerciceUserRepository $exerciceUserRepository, Avancement $avancement, $exercice_id, $flag)
    {
        $exercice_user = $exerciceUserRepository->findOneByExerciceAndUser($exercice_id,  $this->getUser()->getId());
        if ($exercice_user && $flag == 0) {
            return $this->redirectToRoute(
                'app_exercice.result',
                [
                    'score' => $exercice_user->getScore(),
                    'exercice_id' => $exercice_id,
                ]
            );
        }

        $form = $this->createForm(PracticeType::class, null, ['exercice_id' => $exercice_id]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $response = $form->getData();
            $session->set('reponse', $response);

            $this->flashMessage->add("success", "Vos réponses sont bien enregistrées !");
            $exercice = $this->exerciceRepository->find($exercice_id);
            $questions = $exercice->getQuestions();

            $score = 0;
            $number_of_questions = count($questions);
            $i = 1;
            foreach ($questions as $question) {
                if ($response["choix" . $i] == $question->getReponse()) {
                    $score += 100 / $number_of_questions;
                }
                $i++;
            }

            $exerciceUserRepository = $this->entityManager->getRepository(ExerciceUser::class);

            // $exercice_user = $exerciceUserRepository->findOneBy([
            //     'exerciceid' => $exercice_id,
            //     'userid' => $this->getUser()->getId()
            // ]);

            if (!$exercice_user) {
                $exercice_user = new ExerciceUser();
                $exercice_user->setexerciceid($exercice_id);
                $exercice_user->setUserid($this->getUser()->getId());
            }

            if ($exercice_user) {
                if ($score < $exercice_user->getScore())
                    $score = $exercice_user->getScore();
            }

            $exercice_user->setScore($score);

            $this->entityManager->persist($exercice_user);
            $this->entityManager->flush();


            $avancement_value = $avancement->GetUserAvancement($formationRepository->find($exercice->getSection()->getIdformation()), $this->getUser()->getId());
            $avancement->updateUserAvancement($formationRepository->find($exercice->getSection()->getIdformation())->getId(), $this->getUser()->getId(), $avancement_value);

            return $this->redirectToRoute(
                'app_exercice.result',
                [
                    'score' => $score,
                    'exercice_id' => $exercice_id,
                ]
            );
        }

        return $this->render('exercice/practice.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/exercice/{exercice_id}/resultat/{score}", name="app_exercice.result")
     */
    public function result($score, $exercice_id, Avancement $avancement, FormationRepository $formationRepository)
    {
        $exercice = $this->exerciceRepository->find($exercice_id);

        $avancement_value = $avancement->GetUserAvancement($formationRepository->find($exercice->getSection()->getIdformation()), $this->getUser()->getId());

        return $this->render(
            'exercice/result.html.twig',
            [
                'score' => $score,
                'exercice' => $exercice,
                'avancement' => $avancement_value,
                'formation_id' => $formationRepository->find($exercice->getSection()->getIdformation())
            ]
        );
    }
}
