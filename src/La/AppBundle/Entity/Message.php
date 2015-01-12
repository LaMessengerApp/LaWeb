<?php
// src/La/AppBundle/Entity/Message.php

namespace La\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use La\UserBundle\Entity\User;
use La\AppBundle\Entity\Conversation;


/**
 * @ORM\Entity(repositoryClass="La\AppBundle\Entity\MessageRepository")
 * @ORM\Table(name="message")
 */
class Message
{
    /**
   * @ORM\Column(name="id", type="integer")
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  private $id;

  /**
   * @ORM\ManyToOne(targetEntity="La\UserBundle\Entity\User", cascade={"persist"})
   */
  private $author;

  /**
   * @ORM\ManyToOne(targetEntity="La\AppBundle\Entity\Conversation")
   * @ORM\JoinColumn(nullable=false)
   */
  private $conversation;

  /**
   * @ORM\Column(name="text", type="string", length=1024)
   */
  private $text;

  /**
    * @var \DateTime
    *
    * @ORM\Column(name="date", type="datetime")
    */
  private $date;

  public function __construct()
  {
    $this->date = new \Datetime();
  }

  public function setConversation(Conversation $conversation)
  {
    $this->conversation = $conversation;

    return $this;
  }

  public function getConversation()
  {
    return $this->conversation;
  }

  public function __toString()
    {
        return $this->author." : ".$this->text;
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
     * Set text
     *
     * @param string $text
     * @return Message
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get text
     *
     * @return string 
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set author
     *
     * @param \La\UserBundle\Entity\User $author
     * @return Message
     */
    public function setAuthor(\La\UserBundle\Entity\User $author = null)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get author
     *
     * @return \La\UserBundle\Entity\User 
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return Message
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime 
     */
    public function getDate()
    {
        return $this->date;
    }
}
