<?php

namespace App;

/**
 * Class BriefPost
 * Just an average Reddit Post
 *
 * @package App
 */
class BriefPost extends Post
{
    protected $media;
    protected $content_url;
    protected $content_domain;

    public function __construct(array $post_array)
    {
        $this->content_url = $post_array['url'];
        $this->content_domain = $post_array['domain'];

        parent::__construct($post_array);
    }

    public function get_content_url() {return $this->content_url;}
    public function get_content_domain() {return $this->content_domain;}

    public function to_twig_array(): array
    {
        $twig_array = parent::to_twig_array();
        $twig_array['content_url'] = $this->get_content_url();
        $twig_array['content_domain'] = $this->get_content_domain();

        return $twig_array;
    }


}