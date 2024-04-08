<?php

namespace App\Service;

use App\Entity\FormationUser;
use App\Repository\ExerciceUserRepository;
use App\Repository\RessourceRepository;
use App\Repository\RessourceUserRepository;
use App\Repository\SectionRepository;
use Doctrine\ORM\EntityManagerInterface;


class Avancement
{

    private $sectionRepository;
    private $ressourceRepository;
    private $exerciceUserRepository;
    private $ressourceUserRepository;
    private $entityManager;

    public function __construct(
        SectionRepository $sectionRepository,
        RessourceRepository $ressourceRepository,
        ExerciceUserRepository $exerciceUserRepository,
        RessourceUserRepository $ressourceUserRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->sectionRepository = $sectionRepository;
        $this->ressourceRepository = $ressourceRepository;
        $this->exerciceUserRepository = $exerciceUserRepository;
        $this->ressourceUserRepository = $ressourceUserRepository;
        $this->entityManager = $entityManager;
    }

    public function GetUserAvancement($formation_id, $user_id)
    {
        // $avancement = 0;
        $sections = $this->sectionRepository->findSectionByFormationId($formation_id);

        $exercices = [];
        $ressources = [];
        foreach ($sections as $section) {
            $exercices = array_merge($exercices, $section->getExercices()->toArray());
            $ressources = array_merge($ressources, $this->ressourceRepository->findRessourcesBySectionId($section->getId()));
        }

        $exercices_done = [];
        $ressources_done = [];
        foreach ($exercices as $exercice) {
            if ($exercice_user = $this->exerciceUserRepository->findOneByExerciceAndUser($exercice->getId(), $user_id)) {
                if ($exercice_user->getScore() >= 50) {
                    array_push($exercices_done, $exercice);
                }
            }
        }
        foreach ($ressources as $ressource) {
            if ($this->ressourceUserRepository->findOneByRessourceAndUser($ressource->getId(), $user_id)) {
                array_push($ressources_done, $ressource);
            }
        }

        $nb_ressource_exercices_total = count($exercices) + count($ressources);
        $nb_ressource_exercices_termine = count($exercices_done) + count($ressources_done);

        if ($nb_ressource_exercices_total == 0)
            return 0;


        return ($nb_ressource_exercices_termine * 100) / $nb_ressource_exercices_total;
    }

    public function updateUserAvancement($formationId, $userId, $avancementValue)
    {

        $formationUserRepository = $this->entityManager->getRepository(FormationUser::class);

        $formationUser = $formationUserRepository->findOneBy([
            'formationid' => $formationId,
            'userid' => $userId,
        ]);

        if (!$formationUser) {
            $formationUser = new FormationUser();
            $formationUser->setFormationid($formationId);
            $formationUser->setUserid($userId);
        }

        $formationUser->setAvancement($avancementValue);

        $this->entityManager->persist($formationUser);
        $this->entityManager->flush();
    }
}
