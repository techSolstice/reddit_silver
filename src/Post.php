<?php

namespace App;

/**
 * Class Post
 * Basis for other posts, such as Mod Posts, Featured Posts, Ads, etc.
 * @package App
 */
abstract class Post
{
    protected $title;
    protected $author;
    protected $score;
    protected $created_utc;
    protected $over_18;
    protected $permalink;

    public function __construct(array $post_array)
    {
        $this->initialize_fields($post_array);
    }

    protected function initialize_fields(array $post_array) : void
    {
        $this->title = $post_array['title'];
        $this->author = $post_array['author'];
        $this->score = $post_array['score'];
        $this->created_utc = $post_array['created_utc'];
        $this->over_18 = $post_array['over_18'];
        $this->permalink = $post_array['permalink'];
    }

    final public function is_nsfw() : bool
    {
        return $this->over_18 == 'on';
    }

    public function get_title() {return $this->title;}
    public function get_author(){return $this->author;}
    public function get_score() {return $this->score;}
    public function get_date_utc() {return $this->created_utc;}
    public function get_permalink() {return $this->permalink;}

    public function to_twig_array() : array
    {
        $twig_array = [];
        $twig_array['title'] = $this->get_title();
        $twig_array['author'] = $this->get_author();
        $twig_array['score'] = $this->get_score();
        $twig_array['date_created'] = $this->get_date_utc();
        $twig_array['permalink'] = $this->get_permalink();

        return $twig_array;
    }
}