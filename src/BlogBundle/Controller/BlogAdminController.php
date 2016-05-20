<?php

namespace BlogBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Doctrine\Common\Persistence\ObjectManager;
use BlogBundle\Entity\Category;
use BlogBundle\Entity\Post;

class BlogAdminController extends Controller
{
    /**
     * @Route("/admin", name="blog_admin")
     */
    public function indexAction()
    {
        $posts = $this->getDoctrine()
	    ->getRepository('BlogBundle:Post')
	    ->findAll();
	$categories = $this->getDoctrine()
	    ->getRepository('BlogBundle:Category')
	    ->findAll();

        return $this->render(
	    'BlogBundle:BlogAdmin:index.html.twig',
	    array(
               'posts' => $posts,
	       'categories' => $categories,
            )
	);
    }

    /**
     * @Route("/admin/post/{action}/{post_title}", name="blog_admin_post", defaults={"action": "view"}, requirements={"action": "view|edit|delete"})
     */
    public function postAction(Request $request, $post_title, $action)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $post = $em->getRepository('BlogBundle:Post')
            ->findOneByTitle($post_title);

        if (!$post) {
            return $this->createNotFoundException();
        }

	$form = null;

        switch ($action) {
            case 'view':
	        break;
            case 'edit':
                $form = $this->createFormBuilder($post)
		    ->add('title', TextType::class)
		    ->add('category')
		    ->add('content', TextareaType::class)
		    ->add('save', SubmitType::class, array('label' => 'Edit post'))
		    ->getForm();

                $form->handleRequest($request);
		
                if ($form->isSubmitted()) {
		    $em->persist($post);
		    $em->flush();
		    return $this->redirectToRoute('blog_admin');
		}
		$form = $form->createView();
	        break;
            case 'delete':
                $form = $this->createFormBuilder($post)
		    ->add('Yes', SubmitType::class, array('label' => 'Yes'))
		    ->getForm();

                $form->handleRequest($request);

                if ($form->isSubmitted() && $form->get('Yes')->isClicked()) {
		    $em->remove($post);
		    $em->flush();
		    return $this->redirectToRoute('blog_admin');
		}
		$form = $form->createView();
	        break;
        }

        return $this->render(
            'BlogBundle:BlogAdmin:post_'.$action.'.html.twig',
            array(
                'post' => $post,
		'form' => $form,
            )
        );
    }

    /**
     * @Route("/admin/category/{action}/{category_name}", name="blog_admin_category", defaults={"action": "view"}, requirements={"action": "view|edit|delete"})
     */
    public function categoryAction(Request $request, $category_name, $action)
    {
        $category = $this->getDoctrine()
            ->getRepository('BlogBundle:Category')
            ->findOneByName($category_name);

        if (!$category) {
            return $this->createNotFoundException();
        }

        $form = null;
        $posts = null;

        $em = $this->getDoctrine()->getEntityManager();

        switch ($action) {
            case 'view':
                $posts = $this->getDoctrine()
	            ->getRepository('BlogBundle:Post')
  	            ->findByCategory($category->getId());
	        break;
            case 'edit':
                $form = $this->createFormBuilder($category)
		    ->add('name', TextType::class)
		    ->add('save', SubmitType::class, array('label' => 'Edit category'))
		    ->getForm();

                $form->handleRequest($request);

                if ($form->isSubmitted()) {
		    $em->persist($category);
		    $em->flush();
		    return $this->redirectToRoute('blog_admin');
		}
		$form = $form->createView();
                break;
            case 'delete':
                $form = $this->createFormBuilder($category)
		    ->add('Yes', SubmitType::class, array('label' => 'Yes'))
		    ->getForm();

                $form->handleRequest($request);

                if ($form->isSubmitted() && $form->get('Yes')->isClicked()) {
		    $em->remove($category);
		    $em->flush();
		    return $this->redirectToRoute('blog_admin');
		}
		$form = $form->createView();
	        break;
        }

        return $this->render(
            'BlogBundle:BlogAdmin:category_'.$action.'.html.twig',
            array(
                'category' => $category,
		'posts' => $posts,
		'form' => $form,
            )
        );
    }

    /**
     * @Route("/admin/category/add", name="blog_admin_category_add")
     */
    public function categoryAddAction(Request $request)
    {
        $category = new Category();

        $form = $this->createFormBuilder($category)
            ->add('name', TextType::class)
            ->add('save', SubmitType::class, array('label' => 'Create category'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
	    $em = $this->getDoctrine()->getManager();
            $em->persist($category);
	    $em->flush();

            return $this->redirectToRoute('blog_admin');
        }

        return $this->render(
            'BlogBundle:BlogAdmin:category_add.html.twig',
	    array(
	        'form' => $form->createView(),
	    )
        );
    }

    /**
     * @Route("/admin/post/add", name="blog_admin_post_add")
     */
    public function postAddAction(Request $request)
    {
        $post = new Post();

        $form = $this->createFormBuilder($post)
            ->add('title', TextType::class)
	    ->add('category')
            ->add('content', TextareaType::class)
            ->add('save', SubmitType::class, array('label' => 'Create post'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
	    $em = $this->getDoctrine()->getManager();
            $em->persist($post);
	    $em->flush();

            return $this->redirectToRoute('blog_admin');
        }

        return $this->render(
            'BlogBundle:BlogAdmin:post_add.html.twig',
	    array(
	        'form' => $form->createView(),
	    )
        );
    }

    /**
     * @Route("/admin/categories", name="blog_admin_categories")
     */
    public function categoriesAction(Request $request)
    {
        $categories = $this->getDoctrine()
	    ->getRepository('BlogBundle:Category')
	    ->findAll();

        return $this->render(
            'BlogBundle:BlogAdmin:categories.html.twig',
	    array(
	        'categories' => $categories,
	    )
        );
    }

    /**
     * @Route("/admin/posts", name="blog_admin_posts")
     */
    public function postsAction(Request $request)
    {
        $posts = $this->getDoctrine()
	    ->getRepository('BlogBundle:Post')
	    ->findAll();

        return $this->render(
            'BlogBundle:BlogAdmin:posts.html.twig',
	    array(
	        'posts' => $posts,
	    )
        );
    }
}
