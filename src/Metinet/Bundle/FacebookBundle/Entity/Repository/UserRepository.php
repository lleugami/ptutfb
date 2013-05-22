<?php

namespace Metinet\Bundle\FacebookBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * UserRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class UserRepository extends EntityRepository
{
    public function getClassement($id){
        
        $query = $this->getEntityManager()
        ->createQuery('
            SELECT u FROM MetinetFacebookBundle:User u
            ORDER BY u.points DESC'
        );

        try {
            $users = $query->getResult();
            
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
        
        if(is_object($users)){
            
            return 1;
        }
        
        $i = 1;
        foreach ($users as $user){
            
            if($user->getFbUid() == $id){
                
                return $i;
            }
            $i ++;
        }
    }
    
    public function getCountAllUsers() {
    	
    	$query = $this->getEntityManager()
            ->createQuery('
                SELECT count(a) FROM MetinetFacebookBundle:User a'
            );
    	try {
    		$result = $query->getSingleResult();
    	} catch (\Doctrine\ORM\NoResultException $e) {
    		return null;
    	}
    	$bal = $result[1];
    	return $bal;
    }
    
    public function getCountDateAllUsers($date) {
    	 
    		$query = $this->getEntityManager()
            ->createQuery('
                SELECT count(a) FROM MetinetFacebookBundle:User a
                WHERE a.createdAt >= :created_at'
            )->setParameter('created_at', $date);

            try {
                $result = $query->getSingleResult();
            } catch (\Doctrine\ORM\NoResultException $e) {
                return null;
            }
    	$bal = $result[1];
    	return $bal;
    }
    
    public function getClassementUsers($userTmp,$nbUsers = 5){
            
        $query = $this->getEntityManager()
            ->createQuery('
                SELECT u FROM MetinetFacebookBundle:User u
                ORDER BY u.points DESC'
            );
            try {
                $Userstmp = $query->getResult();
                $users = null;
                $i = 1;
                foreach($Userstmp as $tmp){
                    $users[] = Array('rang' => $i, 'id' => $tmp->getId(), 'firstname' => $tmp->getFirstname(), 'lastname' => $tmp->getLastname(), 'picture' => $tmp->getPicture(), 'points' => $tmp->getPoints());
                    $i ++;    
                }
                
            } catch (\Doctrine\ORM\NoResultException $e) {
                return null;
            }   
            
            $i = 0;
            foreach ($users as $user){
                if($user['lastname'] == $userTmp->getLastname() && $user['firstname'] == $userTmp->getFirstname()){
                    $newIdUser = $i;
                }
                
                $i ++;
            }
            
            $i = 0;
            if($nbUsers != 0){
                foreach ($users as $user){
                    if($i < $newIdUser - $nbUsers || $i > $newIdUser + $nbUsers ){
                        $users[$i] = null;
                        unset($users[$i]);

                    }

                    $i ++;
                }
            }
            
            return $users;
    }
    public function getClassementAvecAmis($friends,$idUser,$nbFriends = 2){    
    
        if($friends == null){
            
            if($idUser - 2 <= 0){
                $offset = $idUser;
            }
            else{
                $offset = $idUser - 2;
            }
            
            $query = $this->getEntityManager()
            ->createQuery('
                SELECT u FROM MetinetFacebookBundle:User u
                ORDER BY u.points DESC'
            )->setFirstResult($offset)
             ->setMaxResults(5);
            try {
                $Userstmp = $query->getResult();
                $users = null;
                foreach($Userstmp as $tmp){
                    $users[] = Array('id' => $tmp->getId(), 'firstname' => $tmp->getFirstname(), 'lastname' => $tmp->getLastname(), 'picture' => $tmp->getPicture(), 'points' => $tmp->getPoints());
                }
                return $users;
            } catch (\Doctrine\ORM\NoResultException $e) {
                return null;
            }              
        } else{
            
            $i = 0;
            foreach ($friends as $friend){
                
                $query = $this->getEntityManager()
                ->createQuery('
                SELECT u FROM MetinetFacebookBundle:User u
                WHERE u.fbUid = :id'
                )->setParameter('id',$friend);
                try {
                    $tmp = $query->getSingleResult();
                    if($tmp->getPoints() != 0 && $tmp->getPoints() != null){
                        $users[$i] = Array('id' => $tmp->getId(), 'firstname' => $tmp->getFirstname(), 'lastname' => $tmp->getLastname(), 'picture' => $tmp->getPicture(), 'points' => $tmp->getPoints());
                        
                    }

                } catch (\Doctrine\ORM\NoResultException $e) {
                    return null;
                }
                
                $i ++;
            }
            
            $query = $this->getEntityManager()
            ->createQuery('
            SELECT u FROM MetinetFacebookBundle:User u
            WHERE u.id = :id'
            )->setParameter('id',$idUser);
            try {
                $tmp = $query->getSingleResult();
                $userTmp = $tmp;
                
                if($tmp->getPoints() == null){
                   $tmp->setPoints(0);
                }
                $users[$i]= Array('id' => $tmp->getId(), 'firstname' => $tmp->getFirstname(), 'lastname' => $tmp->getLastname(), 'picture' => $tmp->getPicture(), 'points' => $tmp->getPoints());       
                
            } catch (\Doctrine\ORM\NoResultException $e) {
                return null;
            }
            
            $users = $this->sort_by_key($users, 'points');

            $i = 0;
            foreach ($users as $user){
                if($user['lastname'] == $userTmp->getLastname() && $user['firstname'] == $userTmp->getFirstname()){
                    $newIdUser = $i;
                }
                
                $i ++;
            }
            
            
            $i = 0;
            if($nbFriends != 0){
                foreach ($users as $user){
                    if($i < $newIdUser - $nbFriends || $i > $newIdUser + $nbFriends ){
                        $users[$i] = null;
                        unset($users[$i]);

                    }

                    $i ++;
                }
            }
            
            return $users;
        }
        
    }
    
    public function getClassementAvecAmisByQuizz($friends,$idUser, $idquizz) {
    	$i = 0;
    	//var_dump($idquizz);exit;
    	$cpt = 0;
    	$users= null;

    	//var_dump($idUser);exit;
		//var_dump($friends);exit;


		//var_dump($friends);exit;
    	foreach ($friends as $friend){
    		$query = $this->getEntityManager()
    		->createQuery('
                SELECT u as user FROM MetinetFacebookBundle:User u JOIN  MetinetFacebookBundle:QuizzResult q
                WHERE u.fbUid = :iduser AND q.quizz = :idquizz AND q.dateEnd != :test'
    		)->setParameters(array(
    			'iduser' => $friend,
    			//'iduser' => $friend,
    			'idquizz'  => $idquizz,
    			'test'  => ''
			));
    		try {
    			//L'�xecuton de cette requ�te permet de savor sz 
    			$tmp = $query->getResult();
    			//var_dump($tmp);exit;
    			
    			if(sizeof($tmp) >0) {
    				
    				foreach($tmp as $row){
						
    					//$users[$cpt]["user"] =  $row["user"];
    					$query2 = $this->getEntityManager()
    						->createQuery('
			                SELECT q FROM MetinetFacebookBundle:QuizzResult q 
    						WHERE q.user = :iduser AND q.quizz = :idquizz AND q.dateEnd != :test'
    					)->setParameters(array(
				    			'iduser' => $row["user"]->getId(),
				    			//'iduser' => $friend,
				    			'idquizz'  => $idquizz,
				    			'test'  => ''
						));
    					$tmp2 = $query2->getResult();
    				
    					foreach($tmp2 as $row2){
							//var_dump($row["user"]->getId());
    						//var_dump($row2->getWinPoints());exit;
    						//$users[$cpt]['points'] = $row2->getWinPoints();
    						$users[$cpt] = array('id' => $row["user"]->getId(), 'firstname' => $row["user"]->getFirstname(), 'lastname' => $row["user"]->getLastname(), 'picture' => $row["user"]->getPicture(), 'points' => $row2->getWinPoints());
    					}
    					$cpt++;
						//var_dump($users);
    				}
    			}
    		} catch (\Doctrine\ORM\NoResultException $e) {
    			return null;
    		}
    		$i ++;
    	}
		$query = $this->getEntityManager()
		->createQuery('
                SELECT u FROM MetinetFacebookBundle:User u
				WHERE u.id = :id_user'
		)->setParameter('id_user', $idUser);
		try {
			$user = $query->getSingleResult();
		} catch (\Doctrine\ORM\NoResultException $e) {
			return null;
		}
		$query = $this->getEntityManager()
		->createQuery('
                SELECT q FROM MetinetFacebookBundle:QuizzResult q
				WHERE q.user = :id_user AND q.quizz = :id_quizz AND q.dateEnd != :test'
		)->setParameters(array(

                    'id_user' => $idUser,
                    //'iduser' => $friend,
                    'id_quizz'  => $idquizz,
                    'test'  => ''
						));
		try {
			$quizzResult = $query->getSingleResult();
		} catch (\Doctrine\ORM\NoResultException $e) {
			return null;
				
		}
		$users[$cpt] = array('id' => $user->getId(), 'firstname' => $user->getFirstname(), 'lastname' => $user->getLastname(), 'picture' => $user->getPicture(), 'points' => $quizzResult->getWinPoints());
    	$users = $this->sort_by_key($users, 'points');
    	return $users;
    }
    

    function sort_by_key($array, $index, $desc = 1) {
        $sort = array();

        //préparation d'un nouveau tableau basé sur la clé à trier
        foreach ($array as $key => $val) {
            $sort[$key] = $val[$index];
        }

        //tri par ordre naturel et insensible à la casse
        if($desc == 1){
            arsort($sort);
        }
        else{
            asort($sort);
        }
        

        //formation du nouveau tableau trié selon la clé
        $output = array();

        foreach($sort as $key => $val) {
            $output[] = $array[$key];
        }

        return $output;
    }
 
    public function getLastTenUsers() {
    	$query = $this->getEntityManager() 
    	->createQuery('
                SELECT a FROM MetinetFacebookBundle:User a
                ORDER BY a.createdAt DESC'
    	);
    	try {
    		$result = $query->getResult();
    	} catch (\Doctrine\ORM\NoResultException $e) {
    		return null;
    	}
    	return $result;
    }
    
    /***
     * R�cup�re les 10 premiers Joueurs du quizz
     * 
     */
    public function getClassementTopTenByQuizz($id) {
    	$cpt = 0;
    	$users= null;
    	$query = $this->getEntityManager()
    	->createQuery('
                SELECT a FROM MetinetFacebookBundle:User a JOIN MetinetFacebookBundle:QuizzResult q
                WHERE q.quizz = :id_quizz AND q.user = a.id ORDER BY q.winPoints DESC'

    	)->setParameter('id_quizz', $id)
    	 ->setMaxResults(10);
    	try {
    		$result = $query->getResult();
    		foreach ($result as $row) {	
    		
    			$query = $this->getEntityManager()
    			->createQuery('
                SELECT q FROM MetinetFacebookBundle:QuizzResult q
				WHERE q.user = :id_user AND q.quizz = :id_quizz AND q.dateEnd != :test'
    			)->setParameters(array(
    					'id_user' => $row->getId(),
    					//'iduser' => $friend,
    					'id_quizz'  => $id,
    					'test'  => ''

    			));
    			$quizzResult = $query->getSingleResult();
    			$users[$cpt] = array('id' => $row->getId(), 'firstname' => $row->getFirstname(), 'lastname' => $row->getLastname(), 'picture' => $row->getPicture(), 'points' => $quizzResult->getWinPoints());
    			$cpt++;
    		}

    		
    		return $users;
    	} catch (\Doctrine\ORM\NoResultException $e) {
    		return null;
    	}
    }
}
