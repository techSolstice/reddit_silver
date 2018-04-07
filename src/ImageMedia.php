<?php

class ImageMedia extends Media
{
    public function __construct($media_array)
    {
        parent::__construct($media_array['source']['width'],
                            $media_array['source']['height'],
                            $media_array['source']['url'],
                            $media_array[0]['width'],
                            $media_array[0]['height'],
                            $media_array[0]['url']
        );
    }
}