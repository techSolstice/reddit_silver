<?php

namespace App;


abstract class Post
{
    protected $title;
    protected $author;
    protected $score;
    protected $created_utc;
    protected $over_18;
    protected $url;
    protected $permalink;

    public function __construct(array $post_array)
    {
        $this->initialize_fields($post_array);
    }

    protected function initialize_fields(array $post_array) : void
    {
        $this->title = $post_array['title'];
    }

    final public function is_nsfw() : boolean
    {
        return $this->over_18;
    }

}