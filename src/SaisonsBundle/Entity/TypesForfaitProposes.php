<?php

namespace SaisonsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TypesForfaitProposes
 *
 * @ORM\Table(name="types_forfait_proposes")
 * @ORM\Entity(repositoryClass="SaisonsBundle\Repository\TypesForfaitProposesRepository")
 */
class TypesForfaitProposes
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
