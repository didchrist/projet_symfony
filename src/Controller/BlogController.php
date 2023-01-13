<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Article;
use App\Entity\Comment;
use App\Form\ArticleFormType;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use Symfony\Component\HttpFoundation\Request;


class BlogController extends AbstractController
{
    #[Route('/', name: 'app_blog')]
    public function index(ArticleRepository $repo): Response
    {
        $articles = $repo->findAll();
        return $this->render('blog/index.html.twig', [
            'controller_name' => 'BlogController',
            'articles' => $articles
        ]);
    }
    #[Route('/blog/new', name: "blog_create")]
    #[Route('/blog/{id}/edit', name: "blog_edit")]
    public function form(Article $article = null, Request $request, ArticleRepository $articleRepository): Response
    {
        if (!$article) {
            $article = new Article();
        }
        /* $form = $this->createFormBuilder($article)
            ->add('title')
            ->add('content')
            ->add('image')
            ->getForm(); */

        $form = $this->createForm(ArticleFormType::class, $article);

        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {
            if (!$article->getId()) {
                $article->setCreatedAt(new \DateTime());
            }

            $articleRepository->save($article, true);

            return $this->redirectToRoute('blog_show', ['id' => $article->getId()]);
        }

        return $this->render('blog/create.html.twig', [
            'form' => $form->createView(),
            'editMode' => $article->getId() !== null
        ]);
    }
    #[Route("/blog/{id}", name: "blog_show")]
    public function show($id, ArticleRepository $repo, CommentRepository $commentRepository, Request $request)
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);

        $form->handleRequest($request);

        $article = $repo->find($id);
        if ($form->isSubmitted() and $form->isValid()) {
            $comment->setCreateAt(new \DateTime())
                ->setArticle($article);
            $commentRepository->save($comment, true);

            return $this->redirectToRoute('blog_show', ['id' => $article->getId()]);
        }
        return $this->render('blog/show.html.twig', [
            'article' => $article,
            'commentForm' => $form->createView()
        ]);
    }
}
