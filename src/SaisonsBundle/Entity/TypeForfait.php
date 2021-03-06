<?php

namespace SaisonsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TypeForfait
 *
 * @ORM\Table(name="type_forfait")
 * @ORM\Entity(repositoryClass="SaisonsBundle\Repository\TypeForfaitRepository")
 */
class TypeForfait
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
     * @var string
     *
     * @ORM\Column(name="libelleTypeForfait", type="string", length=255)
     */
    private $libelleTypeForfait;


	/**
    * @var int
    *
    * @ORM\Column(name="nbDanses", type="integer")
    */
    private $nbDanses;
    
    /**
    * @var int
    *
    * @ORM\Column(name="tarifTypeForfait", type="integer")
    */
    private $tarifTypeForfait;

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
     * Set libelleTypeForfait
     *
     * @param string $libelleTypeForfait
     * @return TypeForfait
     */
    public function setLibelleTypeForfait($libelleTypeForfait)
    {
        $this->libelleTypeForfait = $libelleTypeForfait;

        return $this;
    }

    /**
     * Get libelleTypeForfait
     *
     * @return string 
     */
    public function getLibelleTypeForfait()
    {
        return $this->libelleTypeForfait;
    }

    /**
     * Set nbDanses
     *
     * @param integer $nbDanses
     * @return TypeForfait
     */
    public function setNbDanses($nbDanses)
    {
        $this->nbDanses = $nbDanses;

        return $this;
    }

    /**
     * Get nbDanses
     *
     * @return integer 
     */
    public function getNbDanses()
    {
        return $this->nbDanses;
    }

    /**
     * Set tarifTypeForfait
     *
     * @param integer $tarifTypeForfait
     * @return TypeForfait
     */
    public function setTarifTypeForfait($tarifTypeForfait)
    {
        $this->tarifTypeForfait = $tarifTypeForfait;

        return $this;
    }

    /**
     * Get tarifTypeForfait
     *
     * @return integer 
     */
    public function getTarifTypeForfait()
    {
        return $this->tarifTypeForfait;
    }
}
