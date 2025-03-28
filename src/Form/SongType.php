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
                'attr' => ['class' => 'form-input'],
            ])
            ->add('lyrics', TextareaType::class, [
                'label' => 'Paroles',
                'required' => false,
                'attr' => ['class' => 'form-textarea', 'rows' => 4],
            ])
            ->add('isDuo', CheckboxType::class, [
                'label' => 'Duo ?',
                'required' => false,
            ])
            ->add('isDownloaded', CheckboxType::class, [
                'label' => 'Téléchargé ?',
                'required' => false,
            ])
            ->add('hasLyrics', CheckboxType::class, [
                'label' => 'Paroles disponibles ?',
                'required' => false,
            ])
            ->add('person', EntityType::class, [
                'class' => Person::class,
                'choice_label' => 'name',  // Utiliser le nom de l'interprète au lieu de l'ID
                'multiple' => true,
                'expanded' => false, // Utiliser un select multiple au lieu de checkboxes
                'label' => 'Interprètes',
                'attr' => ['class' => 'form-select'],
            ])
            ->add('userSongKnowledge', EntityType::class, [
                'class' => UserSongKnowledge::class,
                'choice_label' => 'name',  // Utiliser un label plus lisible
                'placeholder' => 'Sélectionner une connaissance',
                'label' => 'Connaissance utilisateur',
                'attr' => ['class' => 'form-select'],
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
