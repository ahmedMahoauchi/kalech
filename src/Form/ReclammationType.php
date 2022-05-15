<?php

namespace App\Form;

use App\Entity\Reclammation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
class ReclammationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('theme', ChoiceType::class ,[
                'choices'  => [
                    'Machine' => 'Machine',
                    'Coach' => 'Coach',
                    'Programme' => 'Programme',
                ],
            ])
            ->add('date')
            ->add('description', TextareaType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reclammation::class,
        ]);
    }
}
