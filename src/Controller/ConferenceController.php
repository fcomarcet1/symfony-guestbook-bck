<?php

namespace App\Controller;

use Twig\Environment;
use App\Entity\Comment;
use App\Entity\Conference;
use Psr\Log\LoggerInterface;
use App\Form\CommentFormType;
use App\Repository\CommentRepository;
use App\Repository\ConferenceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class ConferenceController extends AbstractController
{

    private ConferenceRepository $conferenceRepository;
    private CommentRepository $commentRepository;
    private Environment $twig;
    private EntityManagerInterface $entityManager;
    private LoggerInterface $logger;

    public function __construct(
        ConferenceRepository $conferenceRepository,
        CommentRepository $commentRepository, 
        Environment $twig,
        EntityManagerInterface $entityManager,
        LoggerInterface $logger
    ) {
        $this->conferenceRepository = $conferenceRepository;
        $this->commentRepository = $commentRepository;
        $this->twig = $twig;
        $this->entityManager  = $entityManager;
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
     * @Route("/conference/{slug}", name="conference")
     */
    public function show(Request $request, Conference $conference, string $photoDir): Response
    {

        $comment = new Comment();
        $form = $this->createForm(CommentFormType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setConference($conference);

            // upload file
            if ($photo = $form['photo']->getData()) {
                $filename = bin2hex(random_bytes(6)) . '.' . $photo->guessExtension();
                try {
                    $photo->move($photoDir,$filename);
                } catch (FileException $e) {
                    // unable to upload the photo, give up
                    $this->logger->warning(\sprintf('File %s could not be stored in %s', $filename, $photoDir));
                }
                $comment->setPhotoFilename($filename);
            }

            $this->entityManager->persist($comment);
            $this->entityManager->flush();

            return $this->redirectToRoute('conference', ['slug' => $conference->getSlug()]);
        }

        $offset = max(0, $request->query->getInt('offset', 0));
        $paginator = $this->commentRepository->getCommentPaginator($conference, $offset);
        //$conferences = $this->conferenceRepository->findAll();

        return new Response(
            $this->twig->render('conference/show.html.twig', [ 
                'conference'    => $conference,
                'comments'      => $paginator,
                'previous'      => $offset - CommentRepository::PAGINATOR_PER_PAGE,
                'next'          => min(count($paginator), $offset + CommentRepository::PAGINATOR_PER_PAGE),
                'comment_form'  => $form->createView(),
            ])
        );

    }
}
