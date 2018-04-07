<?php

namespace App\Controller;

use App\Service\SearchService;
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

        $threadTreeService->gather_posts();

        return $this->render('main/subreddit.html.twig', [
            'controller_name' => 'MainController',
            'subreddit_name' => $subreddit
        ]);
    }

    /**
     * @Route("/search/{query_string}", name="search")
     * @param SearchService $searchService
     * @param string $query_string
     * @return Response
     */
    public function search_posts(SearchService $searchService, string $query_string) : Response
    {
        $searchService->set_query_string($query_string);
        $searchService->gather_posts();

        return $this->render('main/search.html.twig', [
            'controller_name' => 'MainController'
        ]);
    }

    /**
     * @Route("/r/{subreddit}/{query_string}", name="subreddit_search")
     * @param SearchService $searchService
     * @param string $query_string
     * @return Response
     */
    public function search_subreddit_posts(SearchService $searchService, string $subreddit, string $query_string) : Response
    {
        $searchService->set_path('r/' . $subreddit . '/');
        $searchService->set_query_string($query_string, true);
        $searchService->gather_posts();

        return $this->render('main/search.html.twig', [
            'controller_name' => 'MainController'
        ]);
    }
}
