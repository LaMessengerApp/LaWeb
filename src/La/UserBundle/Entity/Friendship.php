<?php
// src/La/UserBundle/Entity/friendship.php

namespace La\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use La\UserBundle\Entity\User;

/**
 * @ORM\Entity(repositoryClass="La\UserBundle\Entity\FriendshipRepository")
 * @ORM\Table(name="friendship")
 */
class Friendship
{
    /**
   * @ORM\Column(name="id", type="integer")
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  private $id;

  /**
   * @ORM\Column(name="status", type="integer")
   */
  private $status;

  //0 u1(nous) en attente de validationde u2, 1 : u1 en attende de validation de u2(nous), 2 amitié valid 

  /**
   * @ORM\ManyToOne(targetEntity="La\UserBundle\Entity\User", inversedBy="friendships")
   * @ORM\JoinColumn(nullable=false)
   */
  private $user1;

  /**
   * @ORM\ManyToOne(targetEntity="La\UserBundle\Entity\User")
   * @ORM\JoinColumn(nullable=false)
   */
  private $user2;

  public function __construct($status, $user1, $user2) {

    $this->status = $status;
    $this->user1 = $user1;
    $this->user2 = $user2;

  }

  public function getStatus()
  {
    return $this->status;
  }

  public function setStatus($status)
  {
    $this->status = $status;
  }

  public function setUser1($user1)
  {
    $this->user1 = $user1;
  }
  
  public function getUser1()
  {
    return $this->user1;
  }

  public function setUser2($user2)
  {
    $this->user2 = $user2;
  }

  public function getUser2()
  {
    return $this->user2;
  }

  public function getId()
  {
    return $this->id;
  }

  public function __toString()
    {
        return $this->user2->getUsername();
    }
  
}