<?php

namespace App\Controller;

use App\Entity\ChoixReponse;
use App\Entity\Question;
use App\Form\ChoixReponseType;
use App\Form\QuestionType;
use App\Repository\ChoixReponseRepository;
use App\Repository\QuestionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Annotation\Route;

class ChoixReponseController extends AbstractController
{
    private $choixRepository;
    private $questionRepository;
    private $flashMessage;

    public function __construct(ChoixReponseRepository $choixRepository, QuestionRepository $questionRepository, FlashBagInterface $flashMessage)
    {
        $this->choixRepository = $choixRepository;
        $this->questionRepository = $questionRepository;
        $this->flashMessage = $flashMessage;
    }

    /**
     * @Route("/{question_id}/choix", name="app_choix")
     */
    public function index($question_id, Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $question = $this->questionRepository->find($question_id);

        if (!$question) {
            throw $this->createNotFoundException('Aucune question trouvée pour l\'id ' . $question_id);
        }

        $choix = $question->getChoixReponses();

        $form = $this->createForm(QuestionType::class, $question);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $question = $form->getData();

            if ($question->getReponse() <= 0 || $question->getReponse() > count($choix)) {
                $this->flashMessage->add("error", "La réponse précisée ne correspond pas aux choix de réponses existants !");
            } else {
                $entityManager->persist($question);
                $entityManager->flush();
                $this->flashMessage->add("success", "La réponse est bien précisée !");
            }
        }

        return $this->render('choix_reponse/index.html.twig', [
            "choix" => $choix,
            'question' => $question,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{question_id}/choix/add", name="app_choix.add")
     */
    public function addChoix(Request $request, $question_id)
    {
        $choix = new ChoixReponse();
        $form = $this->createForm(ChoixReponseType::class, $choix);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $choix = $form->getData();

            $entityManager = $this->getDoctrine()->getManager();
            $question = $entityManager->getRepository(Question::class)->find($question_id);
            $choix->setQuestion($question);

            $entityManager->persist($choix);
            $entityManager->flush();
            $this->flashMessage->add("success", "Le choix est bien ajouté !");

            return $this->redirectToRoute(
                'app_choix',
                ["question_id" => $question_id]
            );
        }

        return $this->render('choix_reponse/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{question_id}/choix/edit/{id}", name="app_choix.edit")
     */
    public function editChoix(ChoixReponse $choix, Request $request, $question_id)
    {
        $form = $this->createForm(ChoixReponseType::class, $choix);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $choix = $form->getData();
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($choix);
            $entityManager->flush();
            $this->flashMessage->add("success", "Le choix est bien modifié !");

            return $this->redirectToRoute(
                'app_choix',
                ['question_id' => $question_id]
            );
        }

        return $this->render('choix_reponse/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{question_id}/choix/delete/{id}", name="app_choix.delete")
     */
    public function deleteChoix(ChoixReponse $choix, $question_id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($choix);
        $entityManager->flush();
        $this->flashMessage->add("success", "Le choix est bien supprimé !");

        return $this->redirectToRoute(
            'app_choix',
            ["question_id" => $question_id]
        );
    }
}
