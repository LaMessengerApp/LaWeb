<?php
// src/La/UserBundle/Entity/User.php

namespace La\UserBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use La\AppBundle\Entity\Conversation;


/**
 * @ORM\Entity(repositoryClass="La\UserBundle\Entity\Repository\UserRepository")
 * @ORM\Table(name="fos_user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @ORM\OneToMany(targetEntity="Friendship", mappedBy="user1", cascade={"persist"})
     **/
    private $friendships;
    
    /**
     * @ORM\ManyToMany(targetEntity="La\AppBundle\Entity\Conversation", mappedBy="users")
     */
    private $conversations;

    public function __construct()
    {
        parent::__construct();
    }

    public function getFriendships()
    {
        return $this->friendships;
    }

    public function isFriend(\La\UserBundle\Entity\User $otherUser)
    {

    }

    public function validateFriendship(\La\UserBundle\Entity\Friendship $friendship)
    {
        $friendship->setStatus(2);
        return $friendship;
    }

    public function addFriend(\La\UserBundle\Entity\User $newFriend)
    {
        $this->friendships[] = new Friendship(0, $this, $newFriend);
        $this->friendships[] = new Friendship(1, $newFriend, $this);
        return $this;
    }

    public function addConversation(Conversation $conversation)
    {
        //on utilise l'ArrayCollection vraiment comme un tableau
        $this->conversations[] = $Conversation;

        return $this;
    }

    public function removeConversation(Conversation $conversation)
    {
        // Ici on utilise une méthode de l'ArrayCollection, pour supprimer la conv
        $this->conversations->removeElement($conversation);
    }

    // on récupère une liste de conv 
    public function getConversations()
    {
        return $this->conversations;
    }


   
}
