<?php
namespace La\UserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use La\UserBundle\Entity\User;

class LoadUserData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $u1 = new User();
        $u1->setUsername("max");
        $u1->setPlainPassword('max');
        $u1->setEmail("max@max.fr");
        $u1->setEnabled(1);
        $this->addReference('u1', $u1);
        $manager->persist($u1);

        $u2 = new User();
        $u2->setUsername("kharlamm");
        $u2->setPlainPassword('kharlamm');
        $u2->setEmail("kharlamm@max.fr");
        $u2->setEnabled(1);
        $this->addReference('u2', $u2);
        $manager->persist($u2);

        $u3 = new User();
        $u3->setUsername("test");
        $u3->setPlainPassword('test');
        $u3->setEmail("test@max.fr");
        $u3->setEnabled(1);
        $this->addReference('u3', $u3);
        $manager->persist($u3);

        $u4 = new User();
        $u4->setUsername("bonjour");
        $u4->setPlainPassword('bonjour');
        $u4->setEmail("bonjour@max.fr");
        $u4->setEnabled(1);
        $this->addReference('u4', $u4);
        $manager->persist($u4);

        $u1->addFriend($u2);

        $manager->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 1; 
    }
}