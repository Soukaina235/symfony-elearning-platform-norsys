<?php

namespace App\Form;

use App\Entity\Exercice;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

use App\Repository\SectionRepository;

class ExerciceType extends AbstractType
{
    public function __construct(SectionRepository $sectionRepository)
    {
        $this->sectionRepository = $sectionRepository;
    }



    public function buildForm(FormBuilderInterface $builder, array $options): void
    {   $sections = $this->sectionRepository->createQueryBuilder('f')
        ->orderBy('f.id', 'ASC')
        ->getQuery()
        ->getResult();

         $sectionChoices = [];
        foreach ($sections as $section) {
            $sectionChoices[$section->getTitre()] = $section;
                                        }
        $builder
            ->add('titre', TextType::class)
            ->add('section',ChoiceType::class,[
                'choices' => $sectionChoices,
                'label'=>'Section'
                
                ])
            ->add('valider', SubmitType::class); // <input type="submit" value="">
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Exercice::class,
        ]);
    }
}
