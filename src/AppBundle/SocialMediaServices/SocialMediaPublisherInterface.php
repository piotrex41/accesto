<?php

namespace AppBundle\SocialMediaServices;

use AppBundle\Entity\BlogPost;

interface SocialMediaPublisherInterface
{
    /**
     * @param BlogPost $post
     * @return bool
     */
    public function publish(BlogPost $post) : bool;

    /**
     * @param string $name
     * @return bool
     */
    public function canProcess(string $name) : bool;
}