<?php

namespace BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class BlogController extends Controller
{
    /**
     * @Route("/{page}", name="blog_index", defaults={"page" = 1}, requirements={"page": "\d+"})
     */
    public function indexAction($page)
    {
        $posts = $this->getDoctrine()
	    ->getRepository('BlogBundle:Post')
	    ->findAll();

        return $this->render(
	    'BlogBundle:Blog:index.html.twig',
	    array(
	        'page' => $page,
		'posts' => $posts,
            )
	);
    }

    /**
     * @Route("/read/{post_title}", name="blog_post")
     */
    public function readPostAction($post_title)
    {
        $post = $this->getDoctrine()
	    ->getRepository('BlogBundle:Post')
	    ->findOneByTitle($post_title);

        if (!$post) {
	    return $this->createNotFoundException();
	}

        return $this->render(
	    'BlogBundle:Blog:post.html.twig',
	    array(
		'post' => $post
	    )
	);
    }

    /**
     * @Route("/category/{category}", name="blog_category", defaults={"category" = "all"})
     */
    public function categoryAction($category)
    {
        $categories = null;
	$posts = null;

        if ($category == "all") {
            $categories = $this->getDoctrine()
	        ->getRepository('BlogBundle:Category')
    	        ->findAll();
	} else {
	    $category_id = $this->getDoctrine()
	        ->getRepository('BlogBundle:Category')
		->findOneByName($category)->getId();

	    $posts = $this->getDoctrine()
	        ->getRepository('BlogBundle:Post')
		->findByCategory($category_id);
	}

        return $this->render(
	    'BlogBundle:Blog:category.html.twig',
	    array(
	        'category' => $category,
		'categories' => $categories,
		'posts' => $posts,
	    )
	);
    }
}
