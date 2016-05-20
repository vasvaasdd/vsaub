<?php

namespace BlogBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use BlogBundle\Entity\Category;

class LoadCategoryData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
	for ($counter = 0; $counter < 10; $counter++)
	{
	    $category = 'category'.$counter;
	    $$category = new Category();
            $$category->setName('Category Name '.$counter);

            $this->addReference('category-'.$counter, $$category);

            $manager->persist($$category);
	}

        $manager->flush();
    }

    public function getOrder()
    {
        return 1;
    }
}