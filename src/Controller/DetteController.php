<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\User;
use App\Entity\Dette;
use App\Entity\Client;
use App\Entity\Detail;
use App\Entity\Panier;
use App\Repository\DetteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DetteController extends AbstractController
{
    #[Route('/dette', name: 'app_dette')]
    public function index(): Response
    {
        $htmlContent = file_get_contents('../view/dette/index.html');
        return new Response($htmlContent);
    }
    #[Route('/dette/form', name: 'app_dette_form')]
    public function store(): Response
    {
        $htmlContent = file_get_contents('../view/dette/form.html');
        return new Response($htmlContent);
    }
    #[Route('/api/dette', name: 'api_dette')]
    public function getClientData(DetteRepository $detteRepository, PaginatorInterface $paginator, Request $request): JsonResponse
    {
        $page = $request->query->getInt('page', 1);
        $limit = 4;

        $queryBuilder = $detteRepository->createQueryBuilder('c');
        $pagination = $paginator->paginate($queryBuilder, $page, $limit);

        $dettes = $pagination->getItems();
        $total = $pagination->getTotalItemCount();
        $totalPages = ceil($total / $limit);
        $hasMore = $page < $totalPages;

        $data = [];
        foreach ($dettes as $dette) {
            $data[] = [
                'id' => $dette->getId(),
                'montantTotal' => $dette->getMontant(),
                'montantVerse' => $dette->getMontantVerser(),
                'montantRestant' => $dette->getMontantRestant(),
                'statusDette' => $dette->getStatusDettes(),
            ];
        }

        return $this->json([
            'dettes' => $data,
            'total' => $total,
            'totalPages' => $totalPages,
            'page' => $page,
            'hasMore' => $hasMore
        ]);
    }
    #[Route('/api/panier/add', name: 'api_panier_add', methods: ['POST'])]
    public function addToCart(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $articleId = $data['article'];
        $qte = $data['quantity'];

        $article = $em->getRepository(Article::class)->find($articleId);
        if (!$article || $article->getQte() < $qte) {
            return new JsonResponse(['error' => 'Quantité insuffisante ou article invalide'], Response::HTTP_BAD_REQUEST);
        }

        $existingPanier = $em->getRepository(Panier::class)->findOneBy(['article' => $article]);

        if ($existingPanier) {
            $newQuantity = $existingPanier->getQte() + $qte;
            if ($article->getQte() < $newQuantity) {
                return new JsonResponse(['error' => 'Quantité totale insuffisante pour cet article'], Response::HTTP_BAD_REQUEST);
            }
            $existingPanier->setQte($newQuantity);
            $existingPanier->setTotal($article->getPrix() * $newQuantity);
        } else {
            // Nouveau panier
            $panier = new Panier();
            $panier->setArticle($article)
                ->setQte($qte)
                ->setTotal($article->getPrix() * $qte);

            $em->persist($panier);
        }

        $em->flush();

        return new JsonResponse(['message' => 'Article ajouté au panier']);
    }

    #[Route('/api/dette/save', name: 'api_dette_save', methods: ['POST'])]
    public function saveDebt(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $clientId = $data['client'];

        $client = $em->getRepository(Client::class)->find($clientId);
        if (!$client) {
            return new JsonResponse(['error' => 'Client introuvable'], Response::HTTP_BAD_REQUEST);
        }

        $panierItems = $em->getRepository(Panier::class)->findAll();
        if (empty($panierItems)) {
            return new JsonResponse(['error' => 'Le panier est vide'], Response::HTTP_BAD_REQUEST);
        }

        $dette = new Dette();
        $dette->setClient($client);
        $montantTotal = 0;

        foreach ($panierItems as $item) {
            $detail = new Detail();
            $detail->setArticle($item->getArticle())
                ->setQte($item->getQte())
                ->setDept($dette);

            $montantTotal += $item->getTotal();

            $article = $item->getArticle();
            $article->setQte($article->getQte() - $item->getQte());

            $em->persist($detail);
        }

        $dette->setMontant($montantTotal);
        $em->persist($dette);

        foreach ($panierItems as $item) {
            $em->remove($item);
        }

        $em->flush();

        return new JsonResponse(['message' => 'Dette enregistrée avec succès']);
    }
    #[Route('/api/articles', name: 'api_article')]
    public function getArticles(EntityManagerInterface $em): JsonResponse
    {
        $articles = $em->getRepository(Article::class)->findAll();
        $data = [];

        foreach ($articles as $article) {
            $data[] = [
                'id' => $article->getId(),
                'nom' => $article->getLibelle(),
                'prix' => $article->getPrix(),
            ];
        }

        return $this->json(['articles' => $data]);
    }

    #[Route('/api/panier/remove', name: 'api_remove_panier')]
    public function removePanier(EntityManagerInterface $em): JsonResponse
    {
        $panierItems = $em->getRepository(Panier::class)->findAll();
        foreach ($panierItems as $item) {
            $em->remove($item);
        }
        $em->flush();
        return new JsonResponse(['message' => 'Panier vide']);
    }
    #[Route('/api/panier', name: 'api_panier')]
    public function getPanier(EntityManagerInterface $em): JsonResponse
    {
        $panierItems = $em->getRepository(Panier::class)->findAll();
        $data = [];

        foreach ($panierItems as $item) {
            $data[] = [
                'article' => [
                    'nom' => $item->getArticle()->getLibelle(),
                    'prix' => $item->getArticle()->getPrix(),
                ],
                'qte' => $item->getQte(),
                'total' => $item->getTotal(),
            ];
        }

        return $this->json(['panier' => $data]);
    }
    #[Route('/api/clients', name: 'api_clients')]
    public function getClients(EntityManagerInterface $em): JsonResponse
    {
        $clients = $em->getRepository(Client::class)->findAll();
        $data = [];

        foreach ($clients as $client) {
            $data[] = [
                'id' => $client->getId(),
                'surname' => $client->getSurname(),
                'telephone' => $client->getTelephone(),
            ];
        }

        return $this->json(['clients' => $data]);
    }
}
