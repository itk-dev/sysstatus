<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Question;
use App\Form\CategoryType;
use App\Form\QuestionType;
use App\Form\ThemeCategoryType;
use App\Form\ThemeType;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/category")
 */
class CategoryController extends AbstractController
{
    /**
     * @Route("/new", name="category_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $category = new Category();
        $form = $this->createCategoryForm($category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($category);
            $entityManager->flush();

            return $this->redirectToRoute('easyadmin', ['entity' => 'Category', 'action' => 'list']);
        }

        return $this->render('category/new.html.twig', [
            'category' => $category,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="category_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Category $category): Response
    {
        $form = $this->createCategoryForm($category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('easyadmin', ['entity' => 'Category', 'action' => 'list']);
        }

        return $this->render('category/edit.html.twig', [
            'category' => $category,
            'form' => $form->createView(),
        ]);
    }

    private function createCategoryForm(Category $category) {
        $form = $this->createForm(CategoryType::class, $category);
        $form->add('questions', CollectionType::class, [
            'entry_type' => QuestionType::class,
            'entry_options' => ['label' => false],
            'prototype_data' => (new Question())->setSortOrder(0),
            'allow_add' => true,
            'allow_delete' => true,
            'by_reference' => false,
        ]);

        return $form;
    }

    /**
     * @Route("/{id}", name="category_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Category $category, TranslatorInterface $translator): Response
    {
        if (!$this->isGranted('delete', $category)) {
            $this->addFlash('danger', $translator->trans('flash.access_denied'));

            // @TODO: Set referer on remove button.
            $refererUrl = $request->query->get('referer', '');
            return $this->redirect(urldecode($refererUrl));
        }

        if ($this->isCsrfTokenValid('delete'.$category->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($category);
            $entityManager->flush();
        }

        return $this->redirectToRoute('easyadmin', ['entity' => 'Category', 'action' => 'list']);
    }
}
