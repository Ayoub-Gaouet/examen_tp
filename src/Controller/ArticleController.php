<?php

namespace App\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{
    #[Route('/article', name: 'app_article')]
    public function index(ArticleRepository $articleRepository)
    {
        $articles = $articleRepository->findBy([], ['dateDePublication' => 'DESC']);


        return $this->render('articles/index.html.twig', [
            'articles' => $articles,
        ]);
    }


    /**
     * @Route("/article/{id}", name="article_show")
     */
    public function show($id , ManagerRegistry $registry) {
        $article = $registry->getRepository(Article::class)
            ->find($id);
        $date = new \DateTime('now');
        echo $date->format('Y-m-d H:i:s');

        return $this->render('article/show_article.html.twig',
            array('article' => $article));
    }

    /**
     * @Route("/article/new", name="new_article")
     * Method({"GET", "POST"})
     */
    public function new(Request $request , ManagerRegistry $registry) {
        $article = new Article();
        $form = $this->createFormBuilder($article)
            ->add('titre', TextType::class)
            ->add('contenu', TextType::class)
            ->add('dateDePublication', TextType::class)
            ->add('save', SubmitType::class, array(
                'label' => 'Créer')
            )->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $article = $form->getData();

            $entityManager = $registry->getManager();
            $entityManager->persist($article);
            $entityManager->flush();

            return $this->redirectToRoute('article_list');
        }
        return $this->render('article/new_article.html.twig',['form' => $form->createView()]);
    }



}
