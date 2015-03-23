#Là messenger - API

##POST
    /api/login

**Request payload**

>string username

>string password
    
*Log l'utilisateur*
    
    /api/friendrequest/{idFriend}

*Demande d'ami, permet aussi de valider une amitié*

    /api/userpicture

*Upload de la photo de profil*

**Request payload**

>file image
    
    /api/messages

**Request payload**

>[int convId]
>
> int[ ] users  (un seul pour l'instant)
> 
> text text
> 
> int lat
> 
> int long
> 
> [file img]
 
*Post un message*


##GET
    /api/me

*Informations sur l'utilisateur courant*

    /api/isauthentificated

*Retourne l'authentification de l'utilisateur courant*

    /api/users

*Liste des utilisateurs*

    /api/user/{idUser}

*Informtation sur un utilisateur par id*

    /api/userbyusername/{username}

*Information sur un utilisateur pas username*

    /api/conversations
    
*Retourne toutes les conversations de l'utilisateur courant*

##PUT##

    /api/messages/{idMessage}
    
**Request payload**

>idem que pour le post

*Modifier un message. Pour changer le statue (rammasé, lu...)*

##DELETE##

    /api/messages/{idMessage}

*Supprime le message*