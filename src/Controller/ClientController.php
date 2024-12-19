<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\Dette;
use App\Entity\User;
use App\Repository\ClientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ClientController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    #[Route('/clients', name: 'app_client')]
    public function index(): Response
    {
        $htmlContent = file_get_contents('../view/client/index.html');
        return new Response($htmlContent);
    }
    #[Route('/clients/show', name: 'app_client_show')]
    public function show(): Response
    {
        $htmlContent = file_get_contents('../view/client/dette.html');
        return new Response($htmlContent);
    }
    #[Route('/clients/form', name: 'app_client_form')]
    public function form(): Response
    {
        $htmlContent = file_get_contents('../view/client/form.html');
        return new Response($htmlContent);
    }

    #[Route('/api/client', name: 'api_client')]
    public function getClientData(ClientRepository $clientRepository, PaginatorInterface $paginator, Request $request): JsonResponse
    {
        $page = $request->query->getInt('page', 1);
        $limit = 4;

        $queryBuilder = $clientRepository->createQueryBuilder('c');
        $pagination = $paginator->paginate($queryBuilder, $page, $limit);

        $clients = $pagination->getItems();
        $total = $pagination->getTotalItemCount();
        $totalPages = ceil($total / $limit);
        $hasMore = $page < $totalPages;

        $data = [];
        foreach ($clients as $client) {
            $data[] = [
                'id' => $client->getId(),
                'prenom' => $client->getUser() ? $client->getUser()->getPrenom() : 'PAS DE COMPTE',
                'surname' => $client->getSurname(),
                'telephone' => $client->getTelephone(),
                'adresse' => $client->getAdresse(),
                'totalDette' => $client->getTotalDette(),
                'brochureFilename' => $client->getUser() ? $client->getUser()->getBrochureFilename() : null
            ];
        }

        return $this->json([
            'clients' => $data,
            'total' => $total,
            'totalPages' => $totalPages,
            'page' => $page,
            'hasMore' => $hasMore
        ]);
    }

    #[Route('/api/clients/store', name: 'app_client_store', methods: ['POST'])]
    public function store(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $client = new Client();
        $data = $request->request->all();
        $fileKey = $request->files->get('fileKey');

        if (empty($data['surname']) || empty($data['adresse']) || empty($data['telephone'])) {
            return new JsonResponse(["message" => "Les champs 'surname', 'adresse' et 'telephone' sont obligatoires."], 400);
        }

        $client->setSurname($data['surname']);
        $client->setAdresse($data['adresse']);
        $client->setTelephone($data['telephone']);

        if (!empty($data['CreateUser']) && $data['CreateUser'] === 'true') {
            if (empty($data['email']) || empty($data['login']) || empty($data['password'])) {
                return new JsonResponse(["message" => "Les champs 'email', 'login' et 'password' sont obligatoires pour créer un utilisateur."], 400);
            }

            $user = new User();
            $user->setNom($data['nom']);
            $user->setPrenom($data['prenom']);
            $user->setEmail($data['email']);
            $user->setLogin($data['login']);
            $user->setPassword($passwordHasher->hashPassword($user, $data['password']));
            $user->setRoles(['ROLE_CLIENT']);
            $user->setCreateAt(new \DateTimeImmutable());
            $user->setUpdateAt(new \DateTimeImmutable());

            if ($fileKey) {
                $allowedMimeTypes = ['image/jpeg', 'image/png'];
                if (!in_array($fileKey->getMimeType(), $allowedMimeTypes)) {
                    return new JsonResponse(["message" => "Format de fichier non supporté. Utilisez une image JPEG ou PNG."], 400);
                }

                $filename = uniqid() . '.' . $fileKey->guessExtension();
                $fileKey->move($this->getParameter('images_directory'), $filename);
                $user->setBrochureFilename($filename);
            }
            $entityManager->persist($user);
            $client->setUser($user);
        }

        $entityManager->persist($client);
        $entityManager->flush();
        return $this->redirectToRoute('app_client');
    }
    #[Route('/api/client/{id}/dettes', name: 'api_client_dettes')]
    public function getClientDettes(Client $client, Request $request): JsonResponse
    {
        if (!$client) {
            return new JsonResponse(['message' => 'Client non trouvé.'], 404);
        }

        $status = $request->query->get('status');

        $dettes = $this->entityManager->getRepository(Dette::class)->findDetteByClientAndStatus($client->getId(), $status);

        // Préparer les données pour l'affichage
        $detteData = [];
        foreach ($dettes as $dette) {
            $detteData[] = [
                'id' => $dette->getId(),
                'montant' => $dette->getMontant(),
                'montantVerse' => $dette->getMontantVerser(),
                'montantDue' => $dette->getMontantRestant(),
                'date' => $dette->getCreateAt()->format('Y-m-d'),
                'statut' => $dette->getStatusDettes(),
            ];
        }

        return $this->json([
            'client' => [
                'prenom' => $client->getUser() ? $client->getUser()->getPrenom() : 'Pas de prénom',
                'nom' => $client->getUser() ? $client->getUser()->getNom() : 'Pas de nom',
                'telephone' => $client->getTelephone(),
                'montantDue' => $client->getTotalDette(),
                'surname' => $client->getSurname(),
            ],
            'dettesClient' => $detteData,
        ]);
    }
}
