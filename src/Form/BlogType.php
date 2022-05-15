<?php

namespace App\Form;

use App\Entity\Rubrique;
use App\Entity\Blog;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Gregwar\CaptchaBundle\Type\CaptchaType;


class BlogType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre')
            ->add('description')
            ->add('photo', FileType::class, ([
            
                'mapped' => false,
                'label'=>'ajouter votre image',
               ]
              ))

            
            ->add('rubrique',EntityType::class,[
                'class'=>Rubrique::class,
                'choice_label'=>'titre'
                
            ])

            ->add('idUser',EntityType::class,[
                'class'=>User::class,
                'choice_label'=>'id'
                
            ])
            ->add('captcha', CaptchaType::class)

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
         
        ]);
    }
}
