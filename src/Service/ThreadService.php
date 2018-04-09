<?php
namespace App\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Exception;
use Symfony\Component\DependencyInjection\ContainerInterface;
use App\BriefPost;

/**
 * Class ThreadService
 * Service providing methods for Reddit Post-related actions, including Requesting from Reddit API
 * @package App\Service
 */
class ThreadService{
    const API_HOST = 'https://www.reddit.com/';
    protected $subreddit;

    /**
     * Sets the user-input subreddit to use
     * @param $subreddit_name
     */
    public function set_subreddit_name($subreddit_name)
    {
        $this->subreddit = $this->sanitize_subreddit_name($subreddit_name);
    }

    /**
     * Quick sanitization
     * @param $subreddit_name
     * @return mixed|string
     */
    protected function sanitize_subreddit_name($subreddit_name)
    {
        $subreddit_name = filter_var($subreddit_name, FILTER_SANITIZE_URL);

        if ($subreddit_name)
        {
            $subreddit_name = htmlspecialchars($subreddit_name);
        }else{
            $subreddit_name = '';
        }

        return $subreddit_name;
    }

    /**
     * Perform a request on one of the main topics (e.g. New) and obtain a set of posts
     * @param $topic_name
     * @return array
     */
    public function request_topic($topic_name) : array
    {
        #Start a new Guzzle client
        $client = new Client();

        #Attempt a Guzzle request
        try
        {
            $response = $client->request(
                'GET',
                self::API_HOST . $topic_name . '.json'
            );

            $post_array = json_decode($response->getBody(), true);

        }catch (Exception\TransferException $guzzle_ex)
        {
            $post_array = array();
        }

        return $post_array;
    }

    /**
     * Perform a request on one of the subreddits and obtain a set of posts
     * @param $subreddit
     * @return array
     */
    public function request_subreddit($subreddit) : array
    {
        #Start a new Guzzle client
        $client = new Client();

        #Attempt a Guzzle request
        try
        {
            $response = $client->request(
                'GET',
                self::API_HOST . 'r/' . $subreddit . '.json'
            );

            $post_array = json_decode($response->getBody(), true);

        }catch (Exception\TransferException $guzzle_ex)
        {
            $post_array = [];
        }

        return $post_array;
    }

    /**
     * Make the JSON array we retrieved a bit friendlier to use
     * @param $response_post_array
     * @return mixed
     */
    public function massage_post_data($response_post_array)
    {
        return $response_post_array['data']['children'];
    }

    /**
     * Obtain a JSON with some posts of the selected subreddit
     * @return string
     */
    public function retrieve_subreddit_posts_json()
    {
        return $this->to_json($this->retrieve_subreddit_posts());
    }

    /**
     * Obtain a JSON with some posts of the selected topic
     * @param $topic_name
     * @return string
     */
    public function retrieve_topic_posts_json($topic_name)
    {
        return $this->to_json($this->retrieve_topic_posts($topic_name));
    }

    /**
     * Obtain some posts of the selected subreddit
     * @return array
     */
    public function retrieve_subreddit_posts()
    {
        $post_array = $this->request_subreddit($this->subreddit);
        $post_collection = $this->get_post_collection($post_array);

        return $this->to_array($post_collection);
    }

    /**
     * Obtain some posts of the selected topic
     * @param $topic_name
     * @return array
     */
    public function retrieve_topic_posts($topic_name)
    {
        $post_array = $this->request_topic($topic_name);
        return $this->to_array($this->get_post_collection($post_array));
    }

    /**
     * Massages the data of the Reddit API, in array form, and transform sthem into an array of BriefPost objects
     * @param $post_array
     * @return array
     */
    private function get_post_collection($post_array)
    {
        $post_array = $this->massage_post_data($post_array);
        $post_collection = $this->construct_posts($post_array);

        return $post_collection;
    }

    /**
     * Creates BriefPost objects from arrays
     * @param $post_array array Array of Arrays
     * @return array Array of BriefPost objects
     */
    public function construct_posts($post_array)
    {
        unset($post_collection);
        $post_collection = [];

        foreach ($post_array as $single_post_array)
        {
            $new_object = new BriefPost($single_post_array['data']);
            $post_collection[] = $new_object;
        }

        return $post_collection;
    }

    /**
     * Takes Reddit array and returns JSON
     * @param $post_array
     * @return string
     */
    public function to_json($post_array)
    {
        return json_encode($post_array);
    }

    /**
     * Convert BriefPost objects into an array that is also ready for Twig
     * @param $post_collection array An array of BriefPost objects
     * @return array
     */
    public function to_array($post_collection)
    {
        $twig_outer_array = [];
        $twig_index = 0;

        foreach ($post_collection as $brief_post)
        {
            if (!$brief_post->is_nsfw())
            {
                $twig_outer_array[$twig_index++] = $brief_post->to_twig_array();
            }
        }

        return $twig_outer_array;
    }

}