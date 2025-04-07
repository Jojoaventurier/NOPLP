<?php

namespace App\Form;

use App\Entity\Song;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class SongType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre de la chanson',
                'required' => true,
                'attr' => ['class' => 'w-full p-2 border rounded']
            ])
            ->add('isDownloaded', CheckboxType::class, [
                'label' => 'Téléchargée',
                'required' => false,
                'attr' => ['class' => 'rounded text-blue-600 focus:ring-blue-500 w-5 h-5']
            ])
            ->add('userSongKnowledge', ChoiceType::class, [
                'choices' => [
                    'Unknown' => 'unknown',
                    'Little' => 'little',
                    'Well' => 'well',
                    'By heart' => 'by_heart',
                ],
                'placeholder' => 'Choose knowledge level',
                'required' => false,
                'attr' => ['class' => 'w-full mt-1 p-2 border border-gray-300 rounded shadow-sm focus:ring-blue-500 focus:border-blue-500']
            ])
            // Add the artists fields
            ->add('existingPersons', HiddenType::class, [
                'mapped' => false,
                'required' => false,
                'attr' => ['class' => 'artist-ids'], // To help with JavaScript binding
            ])
            ->add('newPersons', HiddenType::class, [
                'mapped' => false,
                'required' => false,
                'attr' => ['class' => 'artist-names'], // To help with JavaScript binding
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Enregistrer',
                'attr' => ['class' => 'px-4 py-2 bg-blue-500 text-white font-semibold rounded hover:bg-blue-600 transition duration-300']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Song::class,
        ]);
    }
}
