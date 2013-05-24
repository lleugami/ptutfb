<?php

namespace Metinet\Bundle\FacebookBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * QuizzRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class QuizzRepository extends EntityRepository
{
	/***
	 * r�cup�re le nombre de participants par quizz
	 * @id : id du quizz
	 */
    public function getNbUserByQuizz($id)
    {
        $query = $this->getEntityManager()
        ->createQuery('
            SELECT q FROM MetinetFacebookBundle:QuizzResult q
            WHERE q.quizz = :id'
        )->setParameter('id', $id);

        try {
            return $query->getResult();
            
        } catch (\Doctrine\ORM\NoResultException $e) {
            return 0;
        }
        
    }
    
    /***
     * R�cup�re le taux moyen de r�ussite sur un quizz
     * @id : id du quizz
     * @nbQuizzResult: nombre de quizz lanc�
     */
    public function getTauxReussiteMoyen($nbQuizzResult,$id)
    {
        if(count($nbQuizzResult) != 0){
            $query = $this->getEntityManager()
            ->createQuery('
                SELECT q FROM MetinetFacebookBundle:Quizz q
                WHERE q.id = :id'
            )->setParameter('id', $id);

            try {
                $quizz = $query->getSingleResult();
            } catch (\Doctrine\ORM\NoResultException $e) {
                return null;
            }

            $totalPoints = $quizz->getWinPoints();

            $tauxReussite = 0;

            foreach($nbQuizzResult as $quizzResult){

                $userPoints = $quizzResult->getWinPoints();
                $tauxReussite += ($userPoints * 100) / $totalPoints;

            }

            return $tauxReussite / count($nbQuizzResult);
        }
        else
        {
            return 0;
        }
        

    }
    /***
     * R�cup�re les 4 derniers quizz
     */
    public function quatreDernierQuizz()
    {
        $query = $this->getEntityManager()
        ->createQuery('
            SELECT q FROM MetinetFacebookBundle:Quizz q
            WHERE q.state != 0
            ORDER BY q.id ASC'
        )->setMaxResults(4);
        
        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }
    /***
     * R�cup�re le dernier quizz en promo
     */
    public function dernierQuizzPromo()
    {   
        $em = $this->getEntityManager();
        $max = $em->createQuery('
            SELECT MAX(q.id) FROM EnzimQuestionBundle:Question q
        ')
        ->getSingleScalarResult();
         
        $query = $this->getEntityManager()
        ->createQuery('
            SELECT q FROM MetinetFacebookBundle:Quizz q
            WHERE q.isPromoted = 1 AND q.state != 0 AND q.id >= :rand
           '
        ) ->setParameter('rand',rand(0,$max))
        ->setMaxResults(1);

        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }   
    
	/***
	 * R�cup�re le nombre total de quizz
	 */
    public function getCountTotQuizz() {    	
    	$query = $this->getEntityManager()
    	->createQuery('
                SELECT count(q) FROM MetinetFacebookBundle:Quizz q'
    	);
    	try {
    		$result = $query->getSingleResult();
    	} catch (\Doctrine\ORM\NoResultException $e) {
    		return null;
    	}
    	$bal = $result[1];
    	return $bal;
    }


	/***
	 * R�cup�re la liste des derniers quizz
	 */
    public function getQuizzTrier($id) {

        $query = $this->getEntityManager()
        ->createQuery('
                SELECT q FROM MetinetFacebookBundle:Quizz q
                WHERE q.theme = :id AND q.state = 1
                ORDER BY q.createdAt DESC'
        )->setParameter('id', $id);
        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }
}
