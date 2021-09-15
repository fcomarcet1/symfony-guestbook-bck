<?php

namespace App\Controller;

use App\Entity\Conference;
use App\Repository\CommentRepository;
use App\Repository\ConferenceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class ConferenceController extends AbstractController
{

    private ConferenceRepository $conferenceRepository;
    private CommentRepository $commentRepository;
    private Environment $twig;

    public function __construct(
        ConferenceRepository $conferenceRepository,
        CommentRepository $commentRepository, 
        Environment $twig
    ) {
        $this->conferenceRepository = $conferenceRepository;
        $this->commentRepository = $commentRepository;
        $this->twig = $twig;
    }

    /**
     * @Route("/", name="homepage")
     */
    public function index(): Response
    {
        $conferences = $this->conferenceRepository->findAll();

        return new Response(
            $this->twig->render('conference/index.html.twig', ['conferences' => $conferences])
        );
    }

    /**
     * @Route("/conference/{id}", name="conference")
     */
    public function show(int $id): Response
    {
        $conference = $this->conferenceRepository->findOneBy(['id' => $id ]);
        $comments = $this->commentRepository->findBy(
            ['conference' => $conference], 
            ['createdAt' => 'DESC']
        );
        
        return new Response(
            $this->twig->render('conference/show.html.twig', [
                'conference' => $conference,
                'comments' => $comments
            ])
        );

    }
}
