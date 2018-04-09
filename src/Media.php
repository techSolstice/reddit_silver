<?php

namespace App;

abstract class Media
{
    protected $width;
    protected $height;
    protected $url;

    protected $embed_width;
    protected $embed_height;
    protected $embed_url;       // An embeddable image of the media (even if media is video)

    public function __construct($width, $height, $url, $embed_width, $embed_height, $embed_url)
    {
        $this->width = $width;
        $this->height = $height;
        $this->url = $url;
        $this->embed_width = $embed_width;
        $this->embed_height = $embed_height;
        $this->embed_url = $embed_url;
    }

    public function get_url()
    {
        return $this->url;
    }

    public function get_embed_url()
    {
        return $this->embed_url;
    }

    public function to_array()
    {
        $media_array = [];
        $media_array['url'] = $this->get_url();
        $media_array['embed_url'] = $this->get_embed_url();

        return $media_array;
    }


}