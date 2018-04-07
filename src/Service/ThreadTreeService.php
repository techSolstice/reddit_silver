<?php
namespace App\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

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

    public function request_subreddit()
    {
        #Start a new Guzzle client
        $client = new Client();

        #Attempt a Guzzle request
        try
        {
            $response = $client->request(
                'GET',
                self::API_HOST . 'r/' . $this->subreddit . '.json'
            );

            $streams_array = json_decode($response->getBody(), true);

        }catch (Exception\GuzzleException $guzzle_ex)
        {
            $streams_array = array();
        }

        return $streams_array;
    }

}