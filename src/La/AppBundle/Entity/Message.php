<?php
// src/La/AppBundle/Entity/Message.php

namespace La\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use La\UserBundle\Entity\User;
use La\AppBundle\Entity\Conversation;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\VirtualProperty;


/**
 * @ORM\Entity(repositoryClass="La\AppBundle\Entity\MessageRepository")
 * @ORM\Table(name="message")
 * @ExclusionPolicy("all") 
 */
class Message
{
    /**
   * @ORM\Column(name="id", type="integer")
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   * @expose
   */
  private $id;

  /**
   * @ORM\ManyToOne(targetEntity="La\UserBundle\Entity\User", cascade={"persist"})
   * @expose
   */
  private $author;

  /**
   * @ORM\ManyToOne(targetEntity="La\AppBundle\Entity\Conversation")
   * @ORM\JoinColumn(nullable=false)
   */
  private $conversation;

  /**
   * @ORM\Column(name="text", type="string", length=1024)
   * @expose
   */
  private $text;

  /**
   * @ORM\Column(name="img", type="string", length=1024)
   * @expose
   */
  private $img;

  /**
    * @ORM\Column(type="float")
    * @expose
    */
  protected $latitude;

  /**
    * @ORM\Column(type="float")
    * @expose
    */
  protected $longitude;

  /**
    * @ORM\Column(type="integer")
    * @expose
    */
  // 0 pas recupere, 1 recupere, 2 ouvert
  protected $status;

  /**
    * @var \DateTime
    *
    * @ORM\Column(name="date", type="datetime")
    * @expose
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

    /**
     * Set latitude
     *
     * @param integer $latitude
     * @return Message
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;

        return $this;
    }

    /**
     * Get latitude
     *
     * @return integer 
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * Set longitude
     *
     * @param integer $longitude
     * @return Message
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;

        return $this;
    }

    /**
     * Get longitude
     *
     * @return integer 
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return Message
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set img
     *
     * @param string $img
     * @return Message
     */
    public function setImg($img)
    {
        // générer un nom aléatoire et essayer de deviner l'extension (plus sécurisé)
        $extension = $img->guessExtension();
        $imgName = substr($img->getClientOriginalName(), 0, strlen($img->getClientOriginalName())-strlen($extension)-1);

        if (!$extension) {
            // l'extension n'a pas été trouvée
            $extension = 'bin';
        }
        $dir =  __DIR__.'/../../../../web/messageimages';
        $newImgName = $imgName.'-'.rand(1, 999999).'.'.$extension;
        $img->move($dir, $newImgName);
        
        $this->img = $newImgName;
        return $this;
    }

    /**
     * Get img
     *
     * @return string 
     */
    public function getImg()
    {
        return $this->img;
    }
}
