<?php

namespace App\Form;

use App\Entity\Programme;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
class ProgrammeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nomprogramme')
            ->add('descriptionprogramme')
            ->add('niveauprogramme')
            ->add('niveauprogramme', ChoiceType::class, [
                'choices'  => [
                    'débutant' => 1,
                    'intermédiaire' => 2,
                    'avancé' => 3,
                ],'help' => 'Choisir le niveau du programme',
            ])
           # ->add('genreprogramme')
            ->add('genreprogramme', ChoiceType::class, [
                'choices'  => [
                    'Homme' => 1,
                    'Femme' => 0,
                ],'help' => 'Choisir le genre',
            ])
            #->add('typeprogramme')
            ->add('typeprogramme', ChoiceType::class, [
                'choices'  => [
                    'Full Body' => "Full Body",
                    'Shape Training' => "Shape Training",
                    'Hiit' => "Hiit",
                    'TRX' => "TRX",
                    'Calisthenics' => "Calisthenics",
                    'Crossfit' => "Crossfit",
                    'VIPR' => "VIPR",
                    'Lafay' => "Lafay",
                ],'help' => 'Choisir le type de programme',
            ])
            ->add('imageprogramme', FileType::class, ([
            
                'mapped' => false,
                'label'=>'ajouter votre image',
               ]
              ))

            ->add('iduser', EntityType::class,['class' => 'App\Entity\User'::class, 'choice_label' => 'id'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Programme::class,
        ]);
    }
}
