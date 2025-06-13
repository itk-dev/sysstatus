<?php

namespace App\Form;

use App\Entity\ThemeCategory;
use App\Service\ThemeManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ThemeCategoryType extends AbstractType
{
    public function __construct(private readonly ThemeManager $themeManager)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('sortOrder', null, [
                'label' => 'vægt',
                'required' => true,
                'empty_data' => 0,
            ])
            ->add('category', null, [
                'label' => 'Kategorier',
                'choices' => $this->themeManager->getCategoriesForCurrentUser(),
                'required' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ThemeCategory::class,
        ]);
    }
}
