<?php
namespace App\Form;

use App\Entity\Films;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FilmsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre')
            ->add('description')
            ->add('dateSortie', DateType::class, [
                'widget' => 'single_text', // Champ date simplifié
                'required' => false,
            ])
            ->add('duree')
            ->add('genre')
            ->add('realisateur')
            ->add('acteurs')
            ->add('afficheUrl', null, [
                'label' => 'URL de l\'affiche',
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Films::class,
        ]);
    }
}
