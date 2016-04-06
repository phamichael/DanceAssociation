<?php

namespace SaisonsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SallesSaison
 *
 * @ORM\Table(name="salles_saison")
 * @ORM\Entity(repositoryClass="SaisonsBundle\Repository\SallesSaisonRepository")
 */
class SallesSaison
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
}
