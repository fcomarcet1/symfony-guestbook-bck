<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Message\CommentMessage;
use App\Repository\CommentRepository;
use App\SpamChecker;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class CommentMessageHandler implements MessageHandlerInterface
{
    private EntityManagerInterface $entityManager;
    private SpamChecker $spamChecker;
    private CommentRepository $commentRepository;

    public function __construct(EntityManagerInterface $entityManager, SpamChecker $spamChecker, CommentRepository $commentRepository)
    {
        $this->entityManager = $entityManager;
        $this->spamChecker = $spamChecker;
        $this->commentRepository = $commentRepository;
    }

    public function __invoke(CommentMessage $message)
    {
        // Get comment from message
        $comment = $this->commentRepository->find($message->getId());

        if (!$comment) {
            return;
        }

        // get score
        $commentSpamScore = $this->spamChecker->getSpamScore($comment, $message->getContext());

        if ($commentSpamScore === 2 ) {
            $comment->setState('spam');
        } else {
            $comment->setState('published');
        }
        
        // Save in db new state
        $this->entityManager->flush();
    }
}