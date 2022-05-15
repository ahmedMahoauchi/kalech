<?php

namespace App\Form;

use App\Entity\ProduitShop;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddProduitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('description')
            ->add('prix')
            ->add('qt')
            ->add('image', FileType::class, ([
            
                'mapped' => false,
                'label'=>'ajouter votre image',
               ]
              ))
            ->add('categorie',EntityType::class,['class' => 'App\Entity\CategorieProduit'::class, 'choice_label' => 'nomCategorie'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ProduitShop::class,
        ]);
    }
}
