<?php

namespace App\Controller;

use App\Entity\Exercice;
use App\Entity\Question;
use App\Form\QuestionType;
use App\Repository\QuestionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Annotation\Route;

class QuestionController extends AbstractController
{
    private $questionRepository;
    private $flashMessage;

    public function __construct(QuestionRepository $questionRepository, FlashBagInterface $flashMessage)
    {
        $this->questionRepository = $questionRepository;
        $this->flashMessage = $flashMessage;
    }

    /**
     * @Route("/{exercice_id}/questions", name="app_question")
     */
    public function listQuestionsParExercice($exercice_id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $exercice = $entityManager->getRepository(Exercice::class)->find($exercice_id);
        $questions = $exercice->getQuestions();

        return $this->render('question/index.html.twig', [
            "questions" => $questions,
            'exercice_id' => $exercice_id,
            'exercice' => $exercice
        ]);
    }

    /**
     * @Route("/{exercice_id}/question/show/{id}", name="app_question.show")
     */
    public function showQuestion($id, $exercice_id)
    {
        $question = $this->questionRepository->find($id);
        // $entityManager = $this->getDoctrine()->getManager();
        if (!$question) {
            throw $this->createNotFoundException('Aucune question trouvée pour l\'id ' . $id);
        }

        return $this->render('question/show.html.twig', [
            "question" => $question,
            "exercice_id" => $exercice_id
        ]);
    }

    /**
     * @Route("/{exercice_id}/question/add", name="app_question.add")
     */
    public function addQuestion(Request $request, $exercice_id)
    {
        $question = new Question();
        $form = $this->createForm(QuestionType::class, $question);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $question = $form->getData();
            $entityManager = $this->getDoctrine()->getManager();

            $exercice = $entityManager->getRepository(Exercice::class)->find($exercice_id);
            $question->setExercice($exercice);

            $question->setReponse(0);

            $entityManager->persist($question);
            $entityManager->flush();
            $this->flashMessage->add("success", "Question ajoutée, maintenant ajoutez des choix de réponse !");

            return $this->redirectToRoute(
                'app_question',
                ['exercice_id' => $exercice_id]
            );
        }

        return $this->render('question/add.html.twig', [
            'form' => $form->createView(),
            'exercice_id' => $exercice_id
        ]);
    }

    /**
     * @Route("/{exercice_id}/question/edit/{id}", name="app_question.edit")
     */
    public function editQuestion(Question $question, Request $request, $exercice_id)
    {
        $form = $this->createForm(QuestionType::class, $question);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $question = $form->getData();
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($question);
            $entityManager->flush();
            $this->flashMessage->add("success", "La question est bien modifiée !");

            return $this->redirectToRoute(
                'app_question',
                ['exercice_id' => $exercice_id]
            );
        }

        return $this->render('question/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{exercice_id}/question/delete/{id}", name="app_question.delete")
     */
    public function deleteQuestion(Question $question, $exercice_id)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $choix = $question->getChoixReponses();
        foreach ($choix as $choice) {
            $entityManager->remove($choice);
        }

        $entityManager->remove($question);
        $entityManager->flush();
        $this->flashMessage->add("success", "La question est bien supprimée !");

        return $this->redirectToRoute(
            'app_question',
            ['exercice_id' => $exercice_id]
        );
    }
}
