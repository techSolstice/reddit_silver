<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Service\ThreadTreeService;
use Symfony\Component\DependencyInjection\ContainerInterface;

class MainController extends Controller
{
    /**
     * @Route("/", name="main")
     */
    public function index()
    {
        return $this->render('main/index.html.twig', [
            'controller_name' => 'MainController',
        ]);
    }

    /**
     * @Route("/r/{subreddit}", name="subreddit")
     * @param ThreadTreeService $threadTreeService
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function subreddit_thread(ThreadTreeService $threadTreeService, string $subreddit) : Response
    {
        $threadTreeService->set_subreddit_name($subreddit);

        $subreddit_thread_array = $threadTreeService->request_subreddit($subreddit);

        return $this->render('main/subreddit.html.twig', [
            'controller_name' => 'MainController',
            'subreddit_name' => $subreddit
        ]);
    }
}
