<?php

namespace App\Form;

use App\Entity\Formation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Niveau;

class FormationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('publishedAt', null, [
                'label' => 'Date de parution',
                'required' => true
            ])
            ->add('title', null, [
                'label' => 'Titre',
                'required' => true
            ])
            ->add('description')
            ->add('miniature')
            ->add('picture')
            ->add('videoId')
            ->add('niveau_id', EntityType::class, [
                'class' => Niveau::class,
                'label' => 'Niveau',
                'choice_label' => 'libelle',
                'multiple' => false,
                'required' => true
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Enregistrer'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Formation::class,
        ]);
    }
}
