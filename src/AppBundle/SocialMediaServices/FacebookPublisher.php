<?php

namespace AppBundle\SocialMediaServices;

use AppBundle\Entity\BlogPost;

class FacebookPublisher implements SocialMediaPublisherInterface
{
    const SM_NAME = 'facebook';

    /**
     * @param BlogPost $post
     * @return bool
     */
    public function publish(BlogPost $post) : bool
    {
        // FIXME: Here we public our Blog Post on Facebook

        return true;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function canPublish(string $name): bool
    {
        if (strtolower($name) === self::SM_NAME) {
            return true;
        }

        return false;
    }
}