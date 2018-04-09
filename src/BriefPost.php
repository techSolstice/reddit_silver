<?php

namespace App;

use App\VideoMedia;
use App\ImageMedia;

/**
 * Class BriefPost
 * Just an average Reddit Post
 *
 * @package App
 */
class BriefPost extends Post
{
    protected $media = null;
    protected $content_url;
    protected $content_domain;

    public function __construct(array $post_array)
    {
        $this->content_url = $post_array['url'];
        $this->content_domain = $post_array['domain'];


        if (array_key_exists('media', $post_array) && !empty($post_array['media']) && !empty($post_array['secure_media_embed']))
        {
            $media_array['html'] = $post_array['secure_media_embed']['content'];
            $media_array['provider_name'] = $post_array['media']['oembed']['provider_name'];

            $media_array['width'] = $post_array['secure_media_embed']['width'];
            $media_array['height'] = $post_array['secure_media_embed']['height'];
            $media_array['thumbnail_width'] = $post_array['media']['oembed']['thumbnail_width'];
            $media_array['thumbnail_height'] = $post_array['media']['oembed']['thumbnail_height'];
            $media_array['thumbnail_url'] = $post_array['media']['oembed']['thumbnail_url'];

            $this->media = new VideoMedia($post_array['secure_media_embed']['media_domain_url'], $media_array);
        }

        parent::__construct($post_array);
    }

    public function get_content_url() {return $this->content_url;}
    public function get_content_domain() {return $this->content_domain;}

    public function to_twig_array(): array
    {
        $twig_array = parent::to_twig_array();
        $twig_array['content_url'] = $this->get_content_url();
        $twig_array['content_domain'] = $this->get_content_domain();
        if (!is_null($this->media))
        {
            $twig_array['media'] = $this->media->to_array();
        }else{
            $twig_array['media'] = '';
        }

        return $twig_array;
    }


}