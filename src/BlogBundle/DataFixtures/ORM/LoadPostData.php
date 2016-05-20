<?php

namespace BlogBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use BlogBundle\Entity\Post;

class LoadPostData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
	for ($counter = 0; $counter < 10; $counter++)
	{
	    $post = 'post'.$counter;
	    $$post = new Post();
            $$post->setTitle('Post Title '.$counter);
            $$post->setContent('Post Content '.$counter);
	    $$post->setCategory($this->getReference('category-'.rand(0, 9)));

            $manager->persist($$post);
	}

        $manager->flush();
    }

    public function getOrder()
    {
        return 2;
    }
}