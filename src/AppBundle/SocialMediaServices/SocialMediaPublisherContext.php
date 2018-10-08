<?php

namespace AppBundle\SocialMediaServices;

use AppBundle\Entity\BlogPost;
use AppBundle\Exception\TargetNotExistsException;

class SocialMediaPublisherContext
{
    /**
     * @var array SocialMediaPublisherInterface
     */
    private $strategies = [];

    /**
     * @param SocialMediaPublisherInterface $socialMediaPublisher
     */
    public function addStrategy(SocialMediaPublisherInterface $socialMediaPublisher)
    {
        $this->strategies[] = $socialMediaPublisher;
    }

    /**
     * @param BlogPost $blogPost
     * @param string $target
     * @return mixed
     * @throws TargetNotExistsException
     */
    public function handle(BlogPost $blogPost, string $target)
    {
        foreach ($this->strategies as $publicher) {
            if ($publicher->canProcess($target)) {
                return $publicher->publish($blogPost);
            }
        }

        throw new TargetNotExistsException('Target which you choose doesn\'t exists');
    }
}