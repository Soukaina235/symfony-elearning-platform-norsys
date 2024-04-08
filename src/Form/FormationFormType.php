<?php

namespace App\Form;

use App\Entity\Formation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class FormationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {


        $builder
            ->add('titre', TextType::class, ["label" => "Titre"])
            ->add('image', FileType::class, [
                "label" => "image",
                'required' => false,
            ])
            ->add('categorie', ChoiceType::class, [
                'choices' => [
                    'Développement Web' => 'dev_web',
                    'Programmation' => 'progmmation',
                    'IA' => 'IA',
                    'Sécurité' => 'securite',
                    'Base de données' => 'bd',
                ],
                'label' => 'Categorie'
            ])
            ->add('Enregistrer', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Formation::class,
        ]);
    }
}
