<?php
namespace App\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use App\BriefPost;

class ThreadTreeService{
    const API_HOST = 'https://www.reddit.com/';
    protected $subreddit;

    public function set_subreddit_name($subreddit_name)
    {
        $this->subreddit = $this->sanitize_subreddit_name($subreddit_name);
    }

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

    public function request_subreddit($subreddit)
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

            $streams_array = json_decode($response->getBody(), true);

        }catch (Exception\GuzzleException $guzzle_ex)
        {
            $streams_array = array();
        }

        return $streams_array;
    }

    public function coalesce_post_array($response_post_array)
    {
        return $response_post_array['data']['children'];
    }

    public function gather_posts()
    {
        $post_array = $this->request_subreddit($this->subreddit);
        $post_array = $this->coalesce_post_array($post_array);
        $post_collection = $this->construct_posts($post_array);
        $this->display_posts($post_collection);
    }

    public function construct_posts($post_array)
    {
        $post_collection = Array();

        foreach ($post_array as $single_post_array)
        {
            $post_collection[] = new BriefPost($single_post_array['data']);
        }

        return $post_collection;
    }

    public function display_posts($post_collection)
    {
        echo $post_collection[0]->get_title();
        echo self::API_HOST . $post_collection[0]->get_permalink();
        /*foreach ($post_collection as $post)
        {
            echo $post->getTitle() . '<br>';
        }*/
    }

}