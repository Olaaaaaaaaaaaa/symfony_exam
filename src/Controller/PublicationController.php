<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\CommentType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\PublicationRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

#[Route('/')]
class PublicationController extends AbstractController
{

    public function __construct(
        private PublicationRepository $publicationRepository,
        private EntityManagerInterface $entityManager,
        private PaginatorInterface $paginator,
        private ParameterBagInterface $parameterBag
    ) {
    }


    #[Route(name: 'app_publication')]
    public function index(Request $request): Response
    {
        $qb = $this->publicationRepository->getQbAll();

        $pagination = $this->paginator->paginate($qb, $request->query->getInt('page', 1), 15);

        return $this->render('publication/index.html.twig', [
            'publications' => $pagination
        ]);
    }

    #[Route('/show/{id}', name: 'app_publication_show')]
    public function detail($id, Request $request): Response
    {

        $publicationEntity = $this->publicationRepository->find($id);

        if ($publicationEntity === null) {
            return $this->redirectToRoute('app_publication');
        }

        $comment = new Comment();
        $comment->setCreatedAt(new \DateTime());
        $comment->setPublication($publicationEntity);

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($comment);
            $this->entityManager->flush();
            return $this->redirectToRoute('app_publication');
        }

        return $this->render('publication/show.html.twig', [
            'publication' => $publicationEntity,
            'form' => $form->createView()
        ]);
    }
}
