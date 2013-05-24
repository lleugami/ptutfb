<?php

namespace Metinet\Bundle\FacebookBundle\Manager;

use Doctrine\ORM\EntityManager;
use Metinet\Bundle\ContestBundle\Entity\User;

/**
 * Sets the session on the request.
 *
 * @author Jonathan GUILLEMAIN <jonathan@novaway.fr>
 */
class FbUserManager
{
    protected $em;
    protected $entityName;
    protected $repository;
    protected $facebook;

    public function __construct(EntityManager $em, $facebook)
    {
        $this->em       = $em;
        $this->facebook = $facebook;
    }

    public function createUserFromUid($fbId)
    {
        $now = date('Y-m-d H:i:s');

        $dbal = $this->em->getConnection();

        $myNull = null;
        $user = array(
            'id'            => null,
            'fb_uid'         => $fbId,
            'firstname'     => null,
            'lastname'      => null,
            'picture'       => "https://graph.facebook.com/".$fbId."/picture",
            'created_at'    => $now,
        );

        $query = 'INSERT INTO user (fb_uid, picture, points, firstname, lastname, created_at) VALUES (:fbUid, :picture, 0, :firstname, :lastname, :created_at);';
        $std = $dbal->prepare($query);

        $std->bindParam(':fbUid', $user['fb_uid'], \PDO::PARAM_STR);
        $std->bindParam(':created_at', $user['created_at'], \PDO::PARAM_STR);
        $std->bindParam(':picture', $user['picture'], \PDO::PARAM_STR);
        $std->bindParam(':firstname', $myNull, \PDO::PARAM_NULL);
        $std->bindParam(':lastname', $myNull, \PDO::PARAM_NULL);
       

        $fbDatas = $this->facebook->api('/me');
        if (!empty($fbDatas)) {


            if (isset($fbDatas['first_name'])) {
                $user['firstname'] = $fbDatas['first_name'];
                $std->bindParam(':firstname', $fbDatas['first_name'], \PDO::PARAM_STR);
            }

            if (isset($fbDatas['last_name'])) {
                $user['lastname'] = $fbDatas['last_name'];
                $std->bindParam(':lastname', $fbDatas['last_name'], \PDO::PARAM_STR);
            }
        }

        $std = $std->execute();

        $user['id'] = $dbal->lastInsertId();

        return $user;
    }

    public function findUserByFbId($fbId)
    {
        $user = null;

        // Cache

        $dbal = $this->em->getConnection();

        $query = sprintf('SELECT u.* FROM user u WHERE u.fb_uid = %s', $dbal->quote($fbId, \PDO::PARAM_STR));

        $std = $dbal->query($query);

        $user = $std->fetch(\PDO::FETCH_ASSOC);

        if (!$user) {
            $user = $this->createUserFromUid($fbId);
        }

        // Cache
        return $user;
    }

    public function getUserFriends($fbId)
    {
        try {
            // Méthode 1
            $lstFriends = $this->facebook->api('/'.$fbId."/friends");

            // Méthode 2
            //$myQuery = "SELECT uid2 FROM friend WHERE uid1=me()";
            //$lstFriends = $this->facebook->api( "/fql?q=" . urlencode($myQuery) );

            return $lstFriends;
        }
        catch (Exception $e) {
            echo "Erreur API FB ".$e;
            return null;
        }
    }
    
    public function getUserFriendsUsingApp($fbId)
    {
            // Méthode 1
            $lstFriends = $this->facebook->api('/'.$fbId."/friends?fields=installed");
            
            $tab = null;
            foreach ($lstFriends['data'] as $friend){
                
                if(isset($friend['installed'])){
                    
                    $tab[] =  $friend['id'];
                    
                }
            }
            // Méthode 2
            //$myQuery = "SELECT uid2 FROM friend WHERE uid1=me()";
            //$lstFriends = $this->facebook->api( "/fql?q=" . urlencode($myQuery) );
            
            return $tab;
            
    }
    
   public function getClassementAvecAmis($id)
    {
        $friends = $this->getUserFriendsUsingApp($id);
        
        $i = 0;
        
        foreach ($friends as $friend){
            var_dump($friend);
        }
        /*foreach ($friends as $friend){
            
            $dbal = $this->em->getConnection();
            
            $idFb = $friend[$i]['id'];
            $query = sprintf('SELECT u.* FROM user u WHERE u.fb_uid = 2');

            $std = $dbal->query($query);

            $user = $std->fetch(\PDO::FETCH_ASSOC);
            
            if(is_object($user)){
                echo $user->getId();
            }
            
            $i ++;
        }*/
    }
    
    public function getUserFb()
    {
        return $this->facebook->api('/me');
    }

}