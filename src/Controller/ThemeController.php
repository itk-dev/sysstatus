<?php

namespace App\Controller;

use App\Entity\Theme;
use App\Entity\ThemeCategory;
use App\Form\ThemeCategoryType;
use App\Form\ThemeType;
use App\Repository\GroupRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/theme")
 */
class ThemeController extends AbstractController
{
    private $groupRepository;

    /**
     * ThemeController constructor.
     */
    public function __construct(GroupRepository $groupRepository)
    {
        $this->groupRepository = $groupRepository;
    }

    /**
     * @Route("/new", name="theme_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $theme = new Theme();
        $form = $this->createThemeForm($theme);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($theme);
            $entityManager->flush();

            return $this->redirectToRoute('easyadmin', ['entity' => 'Theme', 'action' => 'list']);
        }

        return $this->render('theme/new.html.twig', [
            'theme' => $theme,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="theme_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Theme $theme, GroupRepository $groupRepository): Response
    {
        $form = $this->createThemeForm($theme);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('easyadmin', ['entity' => 'Theme', 'action' => 'list']);
        }

        return $this->render('theme/edit.html.twig', [
            'theme' => $theme,
            'form' => $form->createView(),
        ]);
    }

    private function createThemeForm($theme) {
        /* @var \Doctrine\Common\Collections\Collection $availableGroups */
        $user = $this->getUser();
        $availableGroups = $this->groupRepository->findByUser($user);

        $availableGroupOptions = [];
        foreach ($availableGroups as $g) {
            $availableGroupOptions[$g->getName()] = $g;
        }

        $form = $this->createForm(ThemeType::class, $theme);
        $form->add('themeCategories', CollectionType::class, [
            'label' => 'theme.form.theme_categories',
            'entry_type' => ThemeCategoryType::class,
            'required' => false,
            'entry_options' => ['label' => false],
            'prototype_data' => (new ThemeCategory())->setSortOrder(0),
            'allow_add' => true,
            'allow_delete' => true,
            'by_reference' => false,
        ]);
        $form->add('systemGroups', ChoiceType::class, [
            'label' => 'theme.form.system_groups',
            'choices' => $availableGroupOptions,
            'required' => false,
            'attr' => [
                'class' => 'form-control',
            ],
            'multiple' => true,
            'data' => $theme->getSystemGroups()->toArray(),
        ]);
        $form->add('reportGroups', ChoiceType::class, [
            'label' => 'theme.form.report_groups',
            'choices' => $availableGroupOptions,
            'required' => false,
            'attr' => [
                'class' => 'form-control',
            ],
            'multiple' => true,
            'data' => $theme->getReportGroups()->toArray(),
        ]);

        return $form;
    }

    /**
     * @Route("/{id}", name="theme_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Theme $theme, TranslatorInterface $translator): Response
    {
        if (!$this->isGranted('delete', $theme)) {
            $this->addFlash('danger', $translator->trans('flash.access_denied'));

            // @TODO: Set referer on remove button.
            $refererUrl = $request->query->get('referer', '');
            return $this->redirect(urldecode($refererUrl));
        }

        if ($this->isCsrfTokenValid('delete'.$theme->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($theme);
            $entityManager->flush();
        }

        return $this->redirectToRoute('easyadmin', ['entity' => 'Theme', 'action' => 'list']);
    }
}
