<?php

namespace CoursBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Cours
 *
 * @ORM\Table(name="cours")
 * @ORM\Entity(repositoryClass="CoursBundle\Repository\CoursRepository")
 */
class Cours
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
     * @ORM\Column(name="jourCours", type="string")
     */
    private $jourCours;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="heureDebutCours", type="time")
     */
    private $heureDebutCours;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="heureFinCours", type="time")
     */
    private $heureFinCours;


    /* =========================== */
    /* ======== LES CLES : ======= */
    /* =========================== */

    /**
    * @ORM\ManyToMany(targetEntity="SaisonsBundle\Entity\Saison")
    * @ORM\JoinColumn(nullable=false)
    */
    private $saison;

    /**
    * @ORM\ManytoMany(targetEntity="SoireesBundle\Entity\Soiree")
    * @ORM\JoinColumn(nullable=true)
    */
    private $soiree;


    /**
    * @ORM\ManyToOne(targetEntity="SaisonsBundle\Entity\Danse")
    * @ORM\JoinColumn(nullable=false)
    */
    private $danse;


    /**
    * @ORM\ManyToOne(targetEntity="SaisonsBundle\Entity\NiveauDanse")
    * @ORM\JoinColumn(nullable=false)
    */
    private $niveauxDanse;
    

    /**
    * @ORM\ManyToOne(targetEntity="SaisonsBundle\Entity\Salle")
    * @ORM\JoinColumn(nullable=true)
    */
    private $salle;

    /**
    * @ORM\ManyToMany(targetEntity="PersonnesBundle\Entity\ProfesseurAssociation")
    * @ORM\JoinColumn(nullable=false)
    */
    private $professeurAssociation;
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->soiree = new \Doctrine\Common\Collections\ArrayCollection();
        $this->professeurAssociation = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set heureDebutCours
     *
     * @param \DateTime $heureDebutCours
     * @return Cours
     */
    public function setHeureDebutCours($heureDebutCours)
    {
        $this->heureDebutCours = $heureDebutCours;

        return $this;
    }

    /**
     * Get heureDebutCours
     *
     * @return \DateTime 
     */
    public function getHeureDebutCours()
    {
        return $this->heureDebutCours;
    }

    /**
     * Set heureFinCours
     *
     * @param \DateTime $heureFinCours
     * @return Cours
     */
    public function setHeureFinCours($heureFinCours)
    {
        $this->heureFinCours = $heureFinCours;

        return $this;
    }

    /**
     * Get heureFinCours
     *
     * @return \DateTime 
     */
    public function getHeureFinCours()
    {
        return $this->heureFinCours;
    }

    /**
     * Add soiree
     *
     * @param \SoireesBundle\Entity\Soiree $soiree
     * @return Cours
     */
    public function addSoiree(\SoireesBundle\Entity\Soiree $soiree)
    {
        $this->soiree[] = $soiree;

        return $this;
    }

    /**
     * Remove soiree
     *
     * @param \SoireesBundle\Entity\Soiree $soiree
     */
    public function removeSoiree(\SoireesBundle\Entity\Soiree $soiree)
    {
        $this->soiree->removeElement($soiree);
    }

    /**
     * Get soiree
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSoiree()
    {
        return $this->soiree;
    }

    /**
     * Set danse
     *
     * @param \SaisonsBundle\Entity\Danse $danse
     * @return Cours
     */
    public function setDanse(\SaisonsBundle\Entity\Danse $danse)
    {
        $this->danse = $danse;

        return $this;
    }

    /**
     * Get danse
     *
     * @return \SaisonsBundle\Entity\Danse 
     */
    public function getDanse()
    {
        return $this->danse;
    }

    /**
     * Set niveauxDanse
     *
     * @param \SaisonsBundle\Entity\NiveauDanse $niveauxDanse
     * @return Cours
     */
    public function setNiveauxDanse(\SaisonsBundle\Entity\NiveauDanse $niveauxDanse)
    {
        $this->niveauxDanse = $niveauxDanse;

        return $this;
    }

    /**
     * Get niveauxDanse
     *
     * @return \SaisonsBundle\Entity\NiveauDanse 
     */
    public function getNiveauxDanse()
    {
        return $this->niveauxDanse;
    }

    /**
     * Set salle
     *
     * @param \SaisonsBundle\Entity\Salle $salle
     * @return Cours
     */
    public function setSalle(\SaisonsBundle\Entity\Salle $salle = null)
    {
        $this->salle = $salle;

        return $this;
    }

    /**
     * Get salle
     *
     * @return \SaisonsBundle\Entity\Salle 
     */
    public function getSalle()
    {
        return $this->salle;
    }

    /**
     * Add professeurAssociation
     *
     * @param \PersonnesBundle\Entity\ProfesseurAssociation $professeurAssociation
     * @return Cours
     */
    public function addProfesseurAssociation(\PersonnesBundle\Entity\ProfesseurAssociation $professeurAssociation)
    {
        $this->professeurAssociation[] = $professeurAssociation;

        return $this;
    }

    /**
     * Remove professeurAssociation
     *
     * @param \PersonnesBundle\Entity\ProfesseurAssociation $professeurAssociation
     */
    public function removeProfesseurAssociation(\PersonnesBundle\Entity\ProfesseurAssociation $professeurAssociation)
    {
        $this->professeurAssociation->removeElement($professeurAssociation);
    }

    /**
     * Get professeurAssociation
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getProfesseurAssociation()
    {
        return $this->professeurAssociation;
    }

    /**
     * Add saison
     *
     * @param \SaisonsBundle\Entity\Saison $saison
     * @return Cours
     */
    public function addSaison(\SaisonsBundle\Entity\Saison $saison)
    {
        $this->saison[] = $saison;

        return $this;
    }

    /**
     * Remove saison
     *
     * @param \SaisonsBundle\Entity\Saison $saison
     */
    public function removeSaison(\SaisonsBundle\Entity\Saison $saison)
    {
        $this->saison->removeElement($saison);
    }

    /**
     * Get saison
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSaison()
    {
        return $this->saison;
    }

    /**
     * Set jourCours
     *
     * @param string $jourCours
     * @return Cours
     */
    public function setJourCours($jourCours)
    {
        $this->jourCours = $jourCours;

        return $this;
    }

    /**
     * Get jourCours
     *
     * @return string 
     */
    public function getJourCours()
    {
        return $this->jourCours;
    }
}
