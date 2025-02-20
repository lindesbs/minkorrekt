<?php

declare(strict_types=1);

namespace lindesbs\minkorrekt\FormType;

use lindesbs\minkorrekt\Entity\PodcastEpisode;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ListeFilterForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'required' => false,
                'label' => false,
                'attr' => array(
                    'placeholder' => 'Titel'
                )
            ])
            ->add('description', TextType::class, [
                'required' => false,
                'label' => false,
                'attr' => array(
                    'placeholder' => 'Beschreibung'
                )
            ])
            ->add('episode', IntegerType::class, [
                'required' => false,
                'label' => false,
                'attr' => array(
                    'placeholder' => 'Episodennummer'
                )
            ])
            ->add('Suchen', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PodcastEpisode::class,
        ]);
    }
}
