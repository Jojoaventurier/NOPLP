<?php

namespace App\Form;

use App\Entity\Song;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SongType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre de la chanson',
                'required' => true,
            ])
            ->add('isDownloaded', CheckboxType::class, [
                'label' => 'Téléchargée',
                'required' => false,
            ])
            ->add('userSongKnowledge', ChoiceType::class, [
                'choices'  => [
                    'Unknown' => 'unknown',
                    'Little' => 'little',
                    'Well' => 'well',
                    'By heart' => 'by_heart',
                ],
                'placeholder' => 'Choose knowledge level',
                'required' => false,
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Enregistrer',
            ]);
        
        // Les artistes sont gérés via le JS : récupérés dans le contrôleur via $request->request->all('song')['person']
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Song::class,
        ]);
    }
}
