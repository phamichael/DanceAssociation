<?php

namespace SaisonsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Salle
 *
 * @ORM\Table(name="salle")
 * @ORM\Entity(repositoryClass="SaisonsBundle\Repository\SalleRepository")
 */
class Salle
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
     * @ORM\Column(name="nomSalle", type="string", length=255)
     */
    private $nomSalle;

    /**
     * @var int
     *
     * @ORM\Column(name="capaciteSalle", type="integer")
     */
    private $capaciteSalle;

    /**
     * @var string
     *
     * @ORM\Column(name="adresseSalle", type="string", length=255)
     */
    private $adresseSalle;


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
     * Set nomSalle
     *
     * @param string $nomSalle
     * @return Salle
     */
    public function setNomSalle($nomSalle)
    {
        $this->nomSalle = $nomSalle;

        return $this;
    }

    /**
     * Get nomSalle
     *
     * @return string 
     */
    public function getNomSalle()
    {
        return $this->nomSalle;
    }

    /**
     * Set capaciteSalle
     *
     * @param integer $capaciteSalle
     * @return Salle
     */
    public function setCapaciteSalle($capaciteSalle)
    {
        $this->capaciteSalle = $capaciteSalle;

        return $this;
    }

    /**
     * Get capaciteSalle
     *
     * @return integer 
     */
    public function getCapaciteSalle()
    {
        return $this->capaciteSalle;
    }

    /**
     * Set adresseSalle
     *
     * @param string $adresseSalle
     * @return Salle
     */
    public function setAdresseSalle($adresseSalle)
    {
        $this->adresseSalle = $adresseSalle;

        return $this;
    }

    /**
     * Get adresseSalle
     *
     * @return string 
     */
    public function getAdresseSalle()
    {
        return $this->adresseSalle;
    }
    
    // permet dans l'affichage d'un champ d'un formulaire : nomSalle, capaciteSalle
    public function getUniqueName(){
		return sprintf('%s, %s places', $this->nomSalle, $this->capaciteSalle);
	}
}
