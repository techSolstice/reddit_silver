<?php

class VideoMedia extends Media
{
    protected $markup;
    protected $provider;

    public function __construct($url, $media_array)
    {
        $this->markup = $media_array['html'];
        $this->provider = $media_array['provider_name'];

        parent::__construct($media_array['width'],
                            $media_array['height'],
                            $url,
                            $media_array['thumbnail_width'],
                            $media_array['thumbnail_height'],
                            $media_array['thumbnail_url']
        );
    }
}