<?php
// src/La/AppBundle/Entity/Conversation.php

namespace La\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use La\UserBundle\Entity\User;
use La\AppBundle\Entity\Message;


/**
 * @ORM\Entity(repositoryClass="La\AppBundle\Entity\ConversationRepository")
 * @ORM\Table(name="conversation")
 */
class Conversation
{
    /**
   * @ORM\Column(name="id", type="integer")
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  private $id;

  /**
    * @ORM\ManyToMany(targetEntity="La\UserBundle\Entity\User", inversedBy="conversations")
    */
  private $users;

  //date

  public function __toString()
    {
        return $this->id;
    }
  
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->users = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Add users
     *
     * @param \La\UserBundle\Entity\User $users
     * @return Conversation
     */
    public function addUser(\La\UserBundle\Entity\User $users)
    {
        $this->users[] = $users;

        return $this;
    }

    /**
     * Remove users
     *
     * @param \La\UserBundle\Entity\User $users
     */
    public function removeUser(\La\UserBundle\Entity\User $users)
    {
        $this->users->removeElement($users);
    }

    /**
     * Get users
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * Has user
     *
     * @return Boolean
     */
    public function hasUser(\La\UserBundle\Entity\User $user)
    {
        foreach ($this->users as $k => $u) {
            if($user == $u){
                return true;
            }
        }
        return false;
    }
}
