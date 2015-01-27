<?php
namespace La\MessageBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use La\UserBundle\Entity\User;
use La\AppBundle\Entity\Message;
use La\AppBundle\Entity\Conversation;

class LoadMessageData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $u1 = $this->getReference('u1');
        $u2 = $this->getReference('u2');
        $u3 = $this->getReference('u3');
        $u4 = $this->getReference('u4');


        // c1 u1 u2
        $c1 = new Conversation();
        $c1->addUser($u1);
        $c1->addUser($u2);
        $manager->persist($c1);

        // c1 u2 u3
        $c2 = new Conversation();
        $c2->addUser($u2);
        $c2->addUser($u3);
        $manager->persist($c2);

        // c1 u3 u4
        $c3 = new Conversation();
        $c3->addUser($u3);
        $c3->addUser($u4);
        $manager->persist($c3);


        // m1 u1 u2
        $m1 = new Message();
        $m1->setAuthor($u1);
        $m1->setConversation($c1);
        $m1->setText("ceci est un super test de message");
        $m1->setStatus(0);
        $m1->setLatitude(0);
        $m1->setLongitude(0);
        $manager->persist($m1);

        // m2 u2 u1
        $m2 = new Message();
        $m2->setAuthor($u2);
        $m2->setConversation($c1);
        $m2->setText("c'est une reponse :)");
        $m2->setStatus(0);
        $m2->setLatitude(0);
        $m2->setLongitude(0);
        $manager->persist($m2);

        // m2 u2 u1
        $m3 = new Message();
        $m3->setAuthor($u2);
        $m3->setConversation($c1);
        $m3->setText("Bla Bla Bla");
        $m3->setStatus(0);
        $m3->setLatitude(0);
        $m3->setLongitude(0);
        $manager->persist($m3);

        $manager->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 2; 
    }
}