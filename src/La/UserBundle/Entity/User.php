<?php
// src/La/UserBundle/Entity/User.php

namespace La\UserBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use La\AppBundle\Entity\Conversation;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Exclude;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\VirtualProperty;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity(repositoryClass="La\UserBundle\Entity\Repository\UserRepository")
 * @ORM\Table(name="fos_user")
 *
 * @ExclusionPolicy("all") 
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

     /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    public $pictureName;

    /**
     * @Assert\File(maxSize="500k")
     */
    public $file;
    

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

    //gestion de la photo
    public function getWebPath()
    {
        return null === $this->pictureName ? null : $this->getUploadDir().'/'.$this->pictureName;
    }

    protected function getUploadRootDir()
    {
        // le chemin absolu du répertoire dans lequel sauvegarder les photos de profil
        return __DIR__.'/../../../../web/'.$this->getUploadDir();
    }

    protected function getUploadDir()
    {
        // get rid of the __DIR__ so it doesn't screw when displaying uploaded doc/image in the view.
        return 'uploads/pictures';
    }
    
    public function uploadProfilePicture()
    {
        // Nous utilisons le nom de fichier original, donc il est dans la pratique 
        // nécessaire de le nettoyer pour éviter les problèmes de sécurité

        // move copie le fichier présent chez le client dans le répertoire indiqué.
        $this->file->move($this->getUploadRootDir(), $this->file->getClientOriginalName());

        // On sauvegarde le nom de fichier
        $this->pictureName = $this->file->getClientOriginalName();
        
        // La propriété file ne servira plus
        $this->file = null;
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
     * Set pictureName
     *
     * @param string $pictureName
     * @return User
     */
    public function setPictureName($pictureName)
    {
        $this->pictureName = $pictureName;

        return $this;
    }

    /**
     * Get pictureName
     *
     * @return string 
     */
    public function getPictureName()
    {
        return $this->pictureName;
    }

    /**
     * Add friendships
     *
     * @param \La\UserBundle\Entity\Friendship $friendships
     * @return User
     */
    public function addFriendship(\La\UserBundle\Entity\Friendship $friendships)
    {
        $this->friendships[] = $friendships;

        return $this;
    }

    /**
     * Remove friendships
     *
     * @param \La\UserBundle\Entity\Friendship $friendships
     */
    public function removeFriendship(\La\UserBundle\Entity\Friendship $friendships)
    {
        $this->friendships->removeElement($friendships);
    }
}
