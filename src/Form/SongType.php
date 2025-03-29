<?php

namespace App\Form;

use App\Entity\Person;
use App\Entity\Song;
use App\Entity\UserSongKnowledge;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SongType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre du morceau',
                'required' => true,
                'attr' => ['class' => 'mt-1 w-full rounded border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500'],
            ])
            ->add('person', EntityType::class, [
                'class' => Person::class,
                'choice_label' => 'name',
                'multiple' => true,  
                'expanded' => false, 
                'label' => 'Interprètes',
                'attr' => [
                    'class' => 'artist-search',
                    'data-placeholder' => 'Recherchez un ou plusieurs interprètes...',
                ],
                'required' => false
            ])
            ->add('newPerson', TextType::class, [
                'label' => 'Ajouter un nouvel interprète',
                'attr' => ['placeholder' => 'Nom du nouvel interprète', 'class' => 'mt-1 w-full rounded border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500'],
                'mapped' => false,
                'required' => false,
            ])
            ->add('isDownloaded', CheckboxType::class, [
                'label' => 'Téléchargé ?',
                'required' => false,
                'attr' => ['class' => 'rounded text-blue-600 focus:ring-blue-500'],
            ])
            ->add('userSongKnowledge', EntityType::class, [
                'class' => UserSongKnowledge::class,
                'choice_label' => 'name',
                'placeholder' => 'Sélectionner une connaissance',
                'label' => 'Connaissance utilisateur',
                'attr' => ['class' => 'mt-1 w-full rounded border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500'],
                'required' => false,
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Enregistrer',
                'attr' => ['class' => 'px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Song::class,
        ]);
    }
}
