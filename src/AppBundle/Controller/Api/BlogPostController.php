<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\BlogPost;
use AppBundle\Form\BlogPostType;
use FOS\RestBundle\Controller\Annotations\Route;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Class BlogPostController.
 */
class BlogPostController extends FOSRestController
{
    /**
     * @ApiDoc(
     *     section="Blog Post",
     *     description="Return complete list of blog posts"
     * )
     *
     * @Route(name="api.blog_post.list", path="/blog-post")
     * @Method("GET")
     * @Security("has_role('ROLE_ADMIN') or has_role('ROLE_USER')")
     *
     * @return \FOS\RestBundle\View\View
     */
    public function listPostsAction()
    {
        $repo = $this->getDoctrine()->getRepository('AppBundle:BlogPost');

        return $this->view($repo->findAll());
    }

    /**
     * @ApiDoc(
     *     section="Blog Post",
     *     description="Create new blog post",
     *     parameters={
     *      {"name"="title", "dataType"="string", "required"=true, "description"="title of post"},
     *      {"name"="content", "dataType"="string", "required"=true, "description"="content"},
     *      {"name"="tags", "dataType"="string", "required"=true, "description"="tags separated by semicolon"},
     *     }
     * )
     *
     * @Route(name="api.blog_post.create", path="/blog-post")
     * @Method("POST")
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @param Request $request
     *
     * @return \FOS\RestBundle\View\View
     */
    public function createPostAction(Request $request)
    {
        $blogPostEntity = new BlogPost();
        $blogPostForm = $this->createForm(BlogPostType::class, $blogPostEntity);

        $blogPostForm->submit($request->request->all());
        if ($blogPostForm->isSubmitted() && $blogPostForm->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($blogPostEntity);
            $entityManager->flush();

            return $this->view($blogPostForm->getData());
        }

        throw new HttpException(400, "Blog post is not valid.");
    }

    /**
     * @ApiDoc(
     *     section="Blog Post",
     *     description="Edit existing blog post",
     *     parameters={
     *      {"name"="title", "dataType"="string", "required"=true, "description"="title of post"},
     *      {"name"="content", "dataType"="string", "required"=true, "description"="content"},
     *      {"name"="tags", "dataType"="string", "required"=true, "description"="tags separated by semicolon"},
     *     }
     * )
     *
     * @Route(name="api.blog_post.edit", path="/blog-post/{id}")
     * @Method("PUT")
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @param BlogPost $blogPost
     * @param Request $request
     * @ParamConverter("blogPost", class="AppBundle:BlogPost")
     *
     * @return \FOS\RestBundle\View\View
     */
    public function editPostAction(BlogPost $blogPost, Request $request)
    {
        $blogPostForm = $this->createForm(BlogPostType::class, $blogPost);

        $blogPostForm->submit($request->request->all());
        if ($blogPostForm->isSubmitted() && $blogPostForm->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($blogPost);
            $entityManager->flush();

            return $this->view($blogPostForm->getData());
        }

        throw new HttpException(400, "Blog post is not valid.");
    }

    /**
     * @ApiDoc(
     *     section="Blog Post",
     *     description="Publish post to specified target"
     * )
     *
     * @Route(name="api.blog_post.publish", path="/blog-post/{id}/{target}")
     * @Method("POST")
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @param BlogPost $blogPost
     * @param string $target
     * @ParamConverter("blogPost", class="AppBundle:BlogPost")
     *
     * @return \FOS\RestBundle\View\View
     * @throws \AppBundle\Exception\TargetNotExistsException
     */
    public function publishPostAction(BlogPost $blogPost, string $target)
    {
        $socialMediaPublisher = $this->get('app.social_media_context');
        $result = $socialMediaPublisher->handle($blogPost, $target);

        return $this->view($result);
    }
}
