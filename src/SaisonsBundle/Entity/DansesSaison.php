<?php

namespace SaisonsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DansesSaison
 *
 * @ORM\Table(name="danses_saison")
 * @ORM\Entity(repositoryClass="SaisonsBundle\Repository\DansesSaisonRepository")
 */
class DansesSaison
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
