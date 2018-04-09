<?php

namespace App\Controller;

use App\Service\SearchService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Service\ThreadService;
use Symfony\Component\DependencyInjection\ContainerInterface;

class MainController extends Controller
{
    /**
     * Main page built using Twig.  Ideally this would also be implemented in JS via API endpoints,
     *     but this is showing usage of Twig.
     * @Route("/", name="main")
     */
    public function index(ThreadService $threadTreeService) : Response
    {
        $threadTreeService->set_subreddit_name('news');
        $news_array = $threadTreeService->retrieve_subreddit_posts();

        $threadTreeService->set_subreddit_name('all');
        $post_array = $threadTreeService->retrieve_subreddit_posts();

        return $this->render('main/index.html.twig', [
            'controller_name' => 'MainController',
            'post_array' => $post_array,
            'news_array' => $news_array
        ]);
    }

    /**
     * Thread endpoint allowing a JSON string to be retrieved by JS
     * @Route("/api/subreddit/{subreddit_name}/", name="apithread")
     * @param ThreadService $threadTreeService
     * @return Response
     */
    public function api_thread(ThreadService $threadTreeService, string $subreddit_name) : Response
    {
        $threadTreeService->set_subreddit_name($subreddit_name);

        return new Response($threadTreeService->retrieve_subreddit_posts_json());
    }

    /**
     * Topic endpoint allowing a JSON string to be retrieved by JS
     * @Route("/api/topic/{topic_name}", name="threads")
     */
    public function api_topic(ThreadService $threadTreeService, string $topic_name)
    {
        return new Response($threadTreeService->retrieve_topic_posts_json($topic_name));
    }

    /**
     * Search endpoint allowing a JSON string to retrieved by JS
     * @Route("/api/search/{query_string}", name="apisearch")
     * @param SearchService $searchService
     * @param string $query_string
     * @return Response
     */
    public function api_search(SearchService $searchService, string $query_string) : Response
    {
        $searchService->set_query_string($query_string);

        return new Response($searchService->gather_posts());
    }
}
