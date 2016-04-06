<?php

namespace PersonnesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;

/**
 * Personne
 *
 * @ORM\Table(name="personne")
 * @ORM\Entity(repositoryClass="PersonnesBundle\Repository\PersonneRepository")
 */
class Personne extends BaseUser
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
     * @var string
     *
     * @ORM\Column(name="nomPersonne", type="string", length=255)
     */
    private $nomPersonne;

    /**
     * @var string
     *
     * @ORM\Column(name="prenomPersonne", type="string", length=255)
     */
    private $prenomPersonne;

    /**
     * @var string
     *
     * @ORM\Column(name="adressePersonne", type="string", length=255)
     */
    private $adressePersonne;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateNaissancePersonne", type="datetime")
     */
    private $dateNaissancePersonne;

    /**
     * @var int
     *
     * @ORM\Column(name="telephonePersonne", type="integer")
     */
    private $telephonePersonne;

    /**
     * @var string
     *
     * @ORM\Column(name="emailPersonne", type="string", length=255)
     */
    private $emailPersonne;


    public function __construct()
    {
        parent::__construct();
    }


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
     * Set nomPersonne
     *
     * @param string $nomPersonne
     * @return Personne
     */
    public function setNomPersonne($nomPersonne)
    {
        $this->nomPersonne = $nomPersonne;

        return $this;
    }

    /**
     * Get nomPersonne
     *
     * @return string 
     */
    public function getNomPersonne()
    {
        return $this->nomPersonne;
    }

    /**
     * Set prenomPersonne
     *
     * @param string $prenomPersonne
     * @return Personne
     */
    public function setPrenomPersonne($prenomPersonne)
    {
        $this->prenomPersonne = $prenomPersonne;

        return $this;
    }

    /**
     * Get prenomPersonne
     *
     * @return string 
     */
    public function getPrenomPersonne()
    {
        return $this->prenomPersonne;
    }

    /**
     * Set adressePersonne
     *
     * @param string $adressePersonne
     * @return Personne
     */
    public function setAdressePersonne($adressePersonne)
    {
        $this->adressePersonne = $adressePersonne;

        return $this;
    }

    /**
     * Get adressePersonne
     *
     * @return string 
     */
    public function getAdressePersonne()
    {
        return $this->adressePersonne;
    }

    /**
     * Set dateNaissancePersonne
     *
     * @param \DateTime $dateNaissancePersonne
     * @return Personne
     */
    public function setDateNaissancePersonne($dateNaissancePersonne)
    {
        $this->dateNaissancePersonne = $dateNaissancePersonne;

        return $this;
    }

    /**
     * Get dateNaissancePersonne
     *
     * @return \DateTime 
     */
    public function getDateNaissancePersonne()
    {
        return $this->dateNaissancePersonne;
    }

    /**
     * Set telephonePersonne
     *
     * @param integer $telephonePersonne
     * @return Personne
     */
    public function setTelephonePersonne($telephonePersonne)
    {
        $this->telephonePersonne = $telephonePersonne;

        return $this;
    }

    /**
     * Get telephonePersonne
     *
     * @return integer 
     */
    public function getTelephonePersonne()
    {
        return $this->telephonePersonne;
    }

    /**
     * Set emailPersonne
     *
     * @param string $emailPersonne
     * @return Personne
     */
    public function setEmailPersonne($emailPersonne)
    {
        $this->emailPersonne = $emailPersonne;

        return $this;
    }

    /**
     * Get emailPersonne
     *
     * @return string 
     */
    public function getEmailPersonne()
    {
        return $this->emailPersonne;
    }
}
