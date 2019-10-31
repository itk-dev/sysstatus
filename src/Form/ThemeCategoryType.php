<?php

namespace App\Form;

use App\Entity\ThemeCategory;
use App\Service\ThemeManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ThemeCategoryType extends AbstractType
{
    private $themeManager;

    /**
     * ThemeCategoryType constructor.
     * @param \App\Service\ThemeManager $themeManager
     */
    public function __construct(ThemeManager $themeManager)
    {
        $this->themeManager = $themeManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('sortOrder', null, [
                'label' => false,
                'required' => true,
                'empty_data' => 0,
            ])
            ->add('category', null, [
                'label' => false,
                'choices' => $this->themeManager->getCategoriesForCurrentUser(),
                'required' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ThemeCategory::class,
        ]);
    }
}
