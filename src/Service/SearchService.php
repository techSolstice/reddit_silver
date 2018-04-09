<?php
namespace App\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Exception;
use Symfony\Component\DependencyInjection\ContainerInterface;
use App\BriefPost;
use App\Service\ThreadService;

class SearchService{
    const API_HOST = 'https://www.reddit.com/';
    const SEARCH_ENDPOINT = 'search.json';

    protected $path = '';
    protected $query_string;
    protected $limit_to_subreddit;
    protected $ThreadTreeService;

    public function __construct(ThreadService $threadService)
    {
        $this->ThreadTreeService = $threadService;
    }

    /**
     * Allows for a path to be prefixed, such as a subreddit or topic allowing a more specific search
     * @param string $path
     */
    public function set_path(string $path)
    {
        $this->path = $this->sanitize_query_string($path);
    }

    /**
     * Provide the string with which we're querying
     * @param $query_string
     * @param bool $limit_to_subreddit Restricts the search to the provided subreddit
     */
    public function set_query_string($query_string, $limit_to_subreddit = false)
    {
        $this->query_string = $this->sanitize_query_string($query_string);
        $this->limit_to_subreddit = $limit_to_subreddit;
    }

    /**
     * Quickly sanitize the query string
     * @param $query_string
     * @return mixed|string
     */
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

    /**
     * Perform a request to retrieve search results
     * @param $query_string
     * @return array|mixed
     */
    protected function request_results($query_string)
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

            $posts_array = json_decode($response->getBody(), true);

        }catch (Exception\TransferException $guzzle_ex)
        {
            $posts_array = [];
        }
        return $posts_array;
    }

    /**
     * Performs the search against Reddit and returns a JSON string
     * @return string
     */
    public function gather_posts()
    {
        $post_array = $this->request_results($this->query_string);

        $post_array = $this->ThreadTreeService->massage_post_data($post_array);

        $post_collection = $this->ThreadTreeService->construct_posts($post_array);

        return $this->ThreadTreeService->to_json($this->ThreadTreeService->to_array($post_collection));
    }
}