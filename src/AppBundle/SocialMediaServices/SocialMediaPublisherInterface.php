<?php

namespace AppBundle\SocialMediaServices;

use AppBundle\Entity\BlogPost;

interface SocialMediaPublisherInterface
{
    public function publish(BlogPost $post) : bool;

    public function canProcess(string $name) : bool;
}