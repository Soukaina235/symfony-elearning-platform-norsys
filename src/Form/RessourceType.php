<?php

namespace App\Form;

use App\Entity\Ressource;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use App\Entity\Section;
use App\Repository\SectionRepository;



class RessourceType extends AbstractType
{
    public function __construct(SectionRepository $sectionRepository)
    {
        $this->sectionRepository = $sectionRepository;
    }


    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $sections = $this->sectionRepository->createQueryBuilder('f')
        ->orderBy('f.id', 'ASC')
        ->getQuery()
        ->getResult();

         $sectionChoices = [];
        foreach ($sections as $section) {
            $sectionChoices[$section->getTitre()] = $section->getId();
    }

        $builder
        ->add('titre',TextType::class,["label"=>"Titre"])
        ->add('lien', FileType::class, [
            "label" => "fichier",
            'required' => false,
            
            ])
        ->add('Type',ChoiceType::class,[
            'choices' => [
                'video' => 'video',
                'power point' => 'ppt',
                'image' => 'image',
                'pdf' => 'pdf',
            ],
            'label' => 'Type'
            ])
        ->add('idsection',ChoiceType::class,[
            'choices' => $sectionChoices,
            'label'=>'Section'
            
            ])
        ->add('Enregistrer',SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ressource::class,
        ]);
    }
}
