<?php
namespace App\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use App\BriefPost;
use App\Service\ThreadTreeService;

class SearchService{
    const API_HOST = 'https://www.reddit.com/';
    const SEARCH_ENDPOINT = 'search.json';

    protected $path = '';
    protected $query_string;
    protected $limit_to_subreddit;

    public function __construct(ThreadTreeService $threadTreeService)
    {
        $this->ThreadTreeService = $threadTreeService;
    }

    public function set_path(string $path)
    {
        $this->path = $this->sanitize_query_string($path);
    }

    public function set_query_string($query_string, $limit_to_subreddit = false)
    {
        $this->query_string = $this->sanitize_query_string($query_string);
        $this->limit_to_subreddit = $limit_to_subreddit;
    }

    protected function sanitize_query_string($query_string)
    {
        $query_string = filter_var($query_string, FILTER_SANITIZE_URL);

        if ($query_string)
        {
            $query_string = htmlspecialchars($query_string);
        }else{
            $query_string = '';
        }

        return $query_string;
    }

    public function request_results($query_string)
    {
        # Start a new Guzzle client
        $client = new Client();

        # Compose the query parameters for the request
        $query_params['q'] = $query_string;

        if ($this->limit_to_subreddit)
        {
            $query_params['restrict_sr'] = 'on';
        }

        # Attempt a Guzzle request
        try
        {
            $response = $client->request(
                'GET',
                self::API_HOST . $this->path . self::SEARCH_ENDPOINT,
                ['query' => $query_params]
            );

            $streams_array = json_decode($response->getBody(), true);

        }catch (Exception\GuzzleException $guzzle_ex)
        {
            $streams_array = array('orange');
        }
        return $streams_array;
    }

    public function gather_posts()
    {
        $post_array = $this->request_results($this->query_string);

        $post_array = $this->ThreadTreeService->coalesce_post_array($post_array);

        $post_collection = $this->ThreadTreeService->construct_posts($post_array);

        $this->ThreadTreeService->display_posts($post_collection);
    }

    public function coalesce_post_array($response_post_array)
    {
        return $response_post_array['data']['children'];
    }


}