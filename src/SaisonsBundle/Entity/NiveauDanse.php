<?php

namespace SaisonsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NiveauDanse
 *
 * @ORM\Table(name="niveau_danse")
 * @ORM\Entity(repositoryClass="SaisonsBundle\Repository\NiveauDanseRepository")
 */
class NiveauDanse
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
     * @ORM\Column(name="libelleNiveauDanse", type="string", length=255)
     */
    private $libelleNiveauDanse;


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
     * Set libelleNiveauDanse
     *
     * @param string $libelleNiveauDanse
     * @return NiveauDanse
     */
    public function setLibelleNiveauDanse($libelleNiveauDanse)
    {
        $this->libelleNiveauDanse = $libelleNiveauDanse;

        return $this;
    }

    /**
     * Get libelleNiveauDanse
     *
     * @return string 
     */
    public function getLibelleNiveauDanse()
    {
        return $this->libelleNiveauDanse;
    }
}
