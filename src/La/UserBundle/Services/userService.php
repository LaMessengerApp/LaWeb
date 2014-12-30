<?php
// UserBundle/Services/userService.php


namespace La\UserBundle\Services;

use Doctrine\ORM\EntityManager;

class userService
{
  private $userId;
  private $newFriendId;
  protected $em;
  

  public function __construct(EntityManager $em, $userId, $newFriendId){
    $this->userId       = $userId;
    $this->newFriendId  = $newFriendId;
    $this->em           = $em;
  }

  /**
   * @param User newFriendId
   * @return bool
   */
  public function addFriendship($userId, $newFriendId)
  {
    
    return 13;
  }
  public function removeFriendship()
  {
    
  }
  public function validateFriendship()
  {
    
  }
}