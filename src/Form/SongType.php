<?php

namespace App\Form;

use App\Entity\Song;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

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
            ->add('lyricsFile', FileType::class, [
                'label' => 'Importer un fichier de paroles (.txt)',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '1M',
                        'mimeTypes' => ['text/plain'],
                        'mimeTypesMessage' => 'Veuillez uploader un fichier texte valide (.txt)',
                    ])
                ],
                'attr' => ['class' => 'block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50']
            ])
            ->add('lyrics', TextareaType::class, [
                'label' => 'Paroles de la chanson',
                'required' => false,
                'attr' => [
                    'class' => 'w-full p-2 border rounded font-mono whitespace-pre-wrap',
                    'rows' => 10,
                    'placeholder' => "Couplet 1 :\nLes mots s'envolent\n\nRefrain :\nEt je chante encore..."
                ]
            ])
            // ->add('save', SubmitType::class, [
            //     'label' => 'Enregistrer',
            //     'attr' => ['class' => 'px-4 py-2 bg-blue-500 text-white font-semibold rounded hover:bg-blue-600 transition duration-300']
            // ])
            ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Song::class,
            'allow_extra_fields' => true, 
        ]);
    }
}
