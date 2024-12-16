<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\ThemeCategory;
use App\Service\ThemeManager;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ThemeCategoryType extends AbstractType
{
    private ThemeManager $themeManager;

    public function __construct(ThemeManager $themeManager)
    {
        $this->themeManager = $themeManager;
    }

    //    public function buildForm(FormBuilderInterface $builder, array $options): void
    //    {
    //        $builder
    //            ->add('category', EntityType::class, [
    //                'class' => Category::class,
    //                'choice_label' => 'name',
    //                'required' => true,
    //            ])
    //            ->add('sortOrder', IntegerType::class, [
    //                'required' => false,
    //                'empty_data' => 0,
    //            ])
    //        ;
    //    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        dd($this->themeManager->getCategoriesForCurrentUser());
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

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ThemeCategory::class,
        ]);
    }
}
