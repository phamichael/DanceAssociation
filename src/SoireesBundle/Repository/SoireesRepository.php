<?php

namespace SoireesBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * SoireesRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class SoireesRepository extends EntityRepository
{
	
	// Récupérer les soirées pour lesquels un prof de id $idprof y participe
	public function getSoirees($idprof){
		$qb = $this->createQueryBuilder('c');
		$qb->join('c.professeurs', 'f')
		   ->where($qb->expr()->eq('f.id', $idprof));
		return $qb;
	}
	//Récupérer les soirées d'un adhérent
	public function getSoireesAdherent($idadh){
		$qb = $this->createQueryBuilder('c');
		$qb->join('c.inscrits', 'f')
		   ->where($qb->expr()->eq('f.id', $idadh));
		return $qb;
	}
}
