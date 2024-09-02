<?php

namespace App\Controller\Admin;



use App\Entity\System;
use App\Repository\CategoryRepository;
use App\Repository\ThemeCategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class AdminContent extends AbstractCrudController
{

    public static function getEntityFqcn(): string
    {
        return System::class;
    }





    /*IMPORT FROM ADMINCONTROLLER TOP*/

    /**
     * AdminController constructor.
     * @param \App\Repository\CategoryRepository $categoryRepository
     * @param \App\Repository\ThemeCategoryRepository $themeCategoryRepository
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager
     * @param \Knp\Component\Pager\PaginatorInterface $paginator
     * @param \Symfony\Contracts\Translation\TranslatorInterface $translator
     */
    public function __construct(
        CategoryRepository $categoryRepository,
        ThemeCategoryRepository $themeCategoryRepository,
        EntityManagerInterface $entityManager,
        PaginatorInterface $paginator,
        TranslatorInterface $translator,
        private AdminUrlGenerator $adminUrlGenerator

    ) {
        $this->categoryRepository = $categoryRepository;
        $this->themeCategoryRepository = $themeCategoryRepository;
        $this->entityManager = $entityManager;
        $this->paginator = $paginator;
        $this->translator = $translator;
        $this->adminUrlGenerator = $adminUrlGenerator;
    }

    // ACTION OVERWRITES

    /**
     * @param $className
     * @param $id
     * @return object|null
     */
    private function getEntity($className, $id)
    {
        return $this->entityManager->getRepository($className)->find($id);
    }

    #[Route('/hest' , name: 'hest') ]
    public function delete(AdminContext $context) : RedirectResponse
    {


        $accessGranted = $this->isGranted('delete', $context->getEntity());

        if (!$accessGranted) {
            $this->addFlash('danger', $this->translator->trans('flash.access_denied'));
            $url = $this->adminUrlGenerator
                ->setController(AdminContent::class)
                ->setAction(Action::INDEX)
                ->generateUrl();

            return $this->redirect($url);
        }


        return parent::delete( $context);
    }






//    public function delete(AdminContext $context)
//    {
////        $entityArray = $this->entity;
//        $entityArray = $context->getEntity()->getFqcn();
//        $test = $context->getCrud()->getControllerFqcn();
////        var_dump($entityArray);
////        var_dump($context->getCrud()->getControllerFqcn());
////
////        die(__FILE__);
////        switch ($entityArray['class']) {
//        switch ($entityArray) {
//            case Report::class:
//            case System::class:
//            case Answer::class:
//            case Theme::class:
//            case ThemeCategory::class:
//            case Category::class:
//            case Question::class:
////                $context = $this->getEntity($entityArray['class'], $_GET['id']);
////                $accessGranted = $this->isGranted('delete', $context);
////
////            if (!$accessGranted) {
////                    $this->addFlash('danger', $this->translator->trans('flash.access_denied'));
////                    $url = $this->adminUrlGenerator
////                        ->setController($test)
////                        ->setAction(Action::INDEX)
////                        ->generateUrl();
////
////                    return $this->redirect($url);
////                }
//                break;
//        }
//
//        return parent::delete($context);
//    }







    /*IMPORT FROM ADMINCONTROLLER BUND*/


}
