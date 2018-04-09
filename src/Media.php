<?php

namespace App;

abstract class Media
{
    protected $width;
    protected $height;
    protected $url;

    protected $embed_width;
    protected $embed_height;
    protected $embed_url;

    protected $media_type;

    public function __construct($width, $height, $url, $embed_width, $embed_height, $embed_url)
    {
        $this->width = $width;
        $this->height = $height;
        $this->url = $url;
        $this->embed_width = $embed_width;
        $this->embed_height = $embed_height;
        $this->embed_url = $embed_url;
    }


}