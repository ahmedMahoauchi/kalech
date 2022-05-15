<?php

namespace App\Form;

use App\Entity\Blog;
use App\Entity\User;
use App\Entity\Comments;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Gregwar\CaptchaBundle\Type\CaptchaType;

class CommentsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('description')
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
            // Configure your form options here
        ]);
    }
}
