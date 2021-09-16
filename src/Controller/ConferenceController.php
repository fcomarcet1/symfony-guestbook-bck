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
     * Get list of all conferences.
     * 
     * @Route("/", name="homepage")
     */
    public function index(): Response
    {
        $conferences = $this->conferenceRepository->findAll();

        return new Response(
            $this->twig->render('conference/index.html.twig', [
                'conferences' => $conferences
            ])
        );
    }

    /**
     * Get conference detail.
     * 
     * @Route("/conference/{id}", name="conference")
     */
    public function show(Request $request, Conference $conference): Response
    {

        $offset = max(0, $request->query->getInt('offset', 0));
        $paginator = $this->commentRepository->getCommentPaginator($conference, $offset);
        $conferences = $this->conferenceRepository->findAll();

        return new Response(
            $this->twig->render('conference/show.html.twig', [

                'conferences' => $conferences, // needed for header in base twig tamplate
                'conference' => $conference,
                'comments' => $paginator,
                'previous' => $offset - CommentRepository::PAGINATOR_PER_PAGE,
                'next' => min(count($paginator), $offset + CommentRepository::PAGINATOR_PER_PAGE),
            ])
        );

    }
}
