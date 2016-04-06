<?php

namespace PersonnesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use PersonnesBundle\Entity\Professeur;

/**
 * ProfesseurAssociation
 *
 * @ORM\Table(name="professeur_association")
 * @ORM\Entity(repositoryClass="PersonnesBundle\Repository\ProfesseurAssociationRepository")
 */

class ProfesseurAssociation 
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
       * @ORM\OneToOne(targetEntity="PersonnesBundle\Entity\Personne", cascade={"persist"})
       * @ORM\JoinColumn(nullable=false)
    */
    private $personne;


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
     * Set personne
     *
     * @param \PersonnesBundle\Entity\Personne $personne
     * @return ProfesseurAssociation
     */
    public function setPersonne(\PersonnesBundle\Entity\Personne $personne)
    {
        $this->personne = $personne;

        return $this;
    }

    /**
     * Get personne
     *
     * @return \PersonnesBundle\Entity\Personne 
     */
    public function getPersonne()
    {
        return $this->personne;
    }
}
