<?php

namespace StagesBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * StageRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class StageRepository extends EntityRepository
{
	
	// Récupérer les stages pour lesquels un prof de l'association de id $idprof y participe
	public function getStages($idprof){
		$qb = $this->createQueryBuilder('c');
		$qb->join('c.professeursAsso', 'f')
		   ->where($qb->expr()->eq('f.id', $idprof));
		return $qb;
	}
	
	// Récupérer les stages pour lesquels un prof externe de id $idprof y participe
	public function getStages_profExterne($idprof){
		$qb = $this->createQueryBuilder('c');
		$qb->join('c.professeurs', 'f')
		   ->where($qb->expr()->eq('f.id', $idprof));
		return $qb;
	}

	//Récupérer les stages de l'adhérent
	public function getStagesAdherent($idadh){
		$qb = $this->createQueryBuilder('c');
		$qb->join('c.stagiaires', 'f')
		   ->where($qb->expr()->eq('f.id', $idadh));
		return $qb;
	}
}
