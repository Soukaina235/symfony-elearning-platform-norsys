<?php

namespace App\Form;

use App\Entity\Section;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use App\Repository\FormationRepository;


class SectionType extends AbstractType
{
    public function __construct(FormationRepository $formationrepository)
    {
        $this->formationrepository = $formationrepository;
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {   
        $formations = $this->formationrepository->createQueryBuilder('f')
        ->orderBy('f.id', 'ASC')
        ->getQuery()
        ->getResult();

         $FormationChoices = [];
        foreach ($formations as $formation) {
            $FormationChoices[$formation->getTitre()] = $formation->getId();
    }
 
        $builder
        ->add('titre',TextType::class,["label"=>"Titre"])
        ->add('description',TextareaType::class,["label"=>"Description"])
        
        ->add('idformation',ChoiceType::class,[
            'choices' => $FormationChoices,
            'label'=>'Formation'
            
            ])
        ->add('Enregistrer',SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Section::class,
        ]);
    }
}
