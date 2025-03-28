<?php

namespace App\Form;

use App\Entity\Person;
use App\Entity\Song;
use App\Entity\UserSongKnowledge;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SongType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('lyrics')
            ->add('isDuo')
            ->add('isDownloaded')
            ->add('hasLyrics')
            ->add('person', EntityType::class, [
                'class' => Person::class,
                'choice_label' => 'id',
                'multiple' => true,
            ])
            ->add('userSongKnowledge', EntityType::class, [
                'class' => UserSongKnowledge::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Song::class,
        ]);
    }
}
