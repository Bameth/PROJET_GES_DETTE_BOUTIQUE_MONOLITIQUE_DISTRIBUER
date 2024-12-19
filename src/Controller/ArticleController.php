<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ArticleController extends AbstractController
{
    #[Route('/article', name: 'article_index')]
    public function index(): Response
    {
        $htmlContent = file_get_contents('../view/article/index.html');
        return new Response($htmlContent);
    }
    #[Route('/article/form', name: 'article_form')]
    public function store(): Response
    {
        $htmlContent = file_get_contents('../view/article/form.html');
        return new Response($htmlContent);
    }
    #[Route('/api/article', name: 'api_articles')]
    public function getClientData(ArticleRepository $articleRepository, PaginatorInterface $paginator, Request $request): JsonResponse
    {
        $page = $request->query->getInt('page', 1);
        $limit = 4;

        $queryBuilder = $articleRepository->createQueryBuilder('a');
        $pagination = $paginator->paginate($queryBuilder, $page, $limit);

        $articles = $pagination->getItems();
        $total = $pagination->getTotalItemCount();
        $totalPages = ceil($total / $limit);
        $hasMore = $page < $totalPages;

        $data = [];
        foreach ($articles as $article) {
            $data[] = [
                'id' => $article->getId(),
                'nom' => $article->getLibelle(),
                'prix' => $article->getPrix(),
                'qte' => $article->getQte(),
            ];
        }

        return $this->json([
            'articles' => $data,
            'total' => $total,
            'totalPages' => $totalPages,
            'page' => $page,
            'hasMore' => $hasMore
        ]);
    }
}
