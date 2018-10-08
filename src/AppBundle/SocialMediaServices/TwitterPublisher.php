<?php

namespace AppBundle\SocialMediaServices;

use AppBundle\Entity\BlogPost;

class TwitterPublisher implements SocialMediaPublisherInterface
{
    const SM_NAME = 'twitter';

    /**
     * @param BlogPost $post
     * @return bool
     */
    public function publish(BlogPost $post) : bool
    {
        // FIXME: Here we public our Blog Post on Twitter

        return true;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function canProcess(string $name): bool
    {
        if (strtolower($name) === self::SM_NAME) {
            return true;
        }

        return false;
    }
}