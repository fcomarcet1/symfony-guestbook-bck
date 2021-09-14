<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ConferenceController extends AbstractController
{
    /**
     * @Route("/", name="conference")
     */
    public function index(Request $request): Response
    {

        // ^/?hello=name
        $greet = '';
        if (null !== $name = $request->query->get('hello')) {
            $greet = \sprintf('<h1>Hello %s</h1>', $name);
        }


        return $this->render('conference/index.html.twig', [
            'name' => $name,
        ]);
    }
}
