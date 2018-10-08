<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\BlogPost;
use AppBundle\Form\BlogPostType;
use FOS\RestBundle\Controller\Annotations\Route;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
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
     * @param int $id
     * @param Request $request
     *
     * @return \FOS\RestBundle\View\View
     */
    public function editPostAction(int $id, Request $request)
    {
        $blogPostEntity = $this->getDoctrine()->getRepository('AppBundle:BlogPost')->find($id);
        if (!$blogPostEntity) {
            throw new HttpException(404, "Blog post was not found.");
        }

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
     *     description="Publish post to specified target"
     * )
     *
     * @Route(name="api.blog_post.publish", path="/blog-post/{post}/{target}")
     * @Method("POST")
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @param int $post
     * @param string $target
     *
     * @return \FOS\RestBundle\View\View
     * @throws \AppBundle\Exception\TargetNotExistsException
     */
    public function publishPostAction(int $post, string $target)
    {
        $blogPostEntity = $this->getDoctrine()->getRepository('AppBundle:BlogPost')->find($post);
        if (!$blogPostEntity) {
            throw new HttpException(404, "Blog post was not found.");
        }

        $socialMediaPublicher = $this->get('app.social_media_context');
        $result = $socialMediaPublicher->handle($blogPostEntity, $target);

        return $this->view($result);
    }
}
