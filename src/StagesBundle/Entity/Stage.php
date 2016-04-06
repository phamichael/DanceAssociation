<?php

namespace StagesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Stage
 *
 * @ORM\Table(name="stage")
 * @ORM\Entity(repositoryClass="StagesBundle\Repository\StageRepository")
 */
class Stage
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
     * @ORM\Column(name="titreStage", type="string", length=255)
     */
    private $titreStage;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateStage", type="datetime")
     */
    private $dateStage;

    /**
     * @var \Time
     *
     * @ORM\Column(name="heureDebutStage", type="time")
     */
    private $heureDebutStage;

    /**
     * @var \Time
     *
     * @ORM\Column(name="heureFinStage", type="time")
     */
    private $heureFinStage;

	
	/**
     * @var int
     *
     * @ORM\Column(name="capaciteStage", type="integer")
     */
    private $capaciteStage;

    /**
     * @var int
     *
     * @ORM\Column(name="montantPreinscription", type="integer")
     */
    private $montantPreinscription;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="delaiPreinscription", type="datetime")
     */
    private $delaiPreinscription;

    /**
     * @var int
     *
     * @ORM\Column(name="tarifStage", type="integer")
     */
    private $tarifStage;

    /**
     * @var string
     *
     * @ORM\Column(name="descriptifStage", type="text")
     */
    private $descriptifStage;


	/* =========================== */
	/* ======== LES CLES : ======= */
	/* =========================== */

	
	/**
	* @ORM\ManyToOne(targetEntity="SaisonsBundle\Entity\Saison", inversedBy="stages")
	* @ORM\JoinColumn(nullable=false)
	*/
	private $saison;


	/**
	* @ORM\ManyToOne(targetEntity="SaisonsBundle\Entity\Danse")
	* @ORM\JoinColumn(nullable=false)
	*/
	private $danse;


	/**
	* @ORM\ManyToMany(targetEntity="SaisonsBundle\Entity\NiveauDanse")
	* @ORM\JoinColumn(nullable=false)
	*/
	private $niveauxDanse;
	

	/**
	* @ORM\ManyToMany(targetEntity="SaisonsBundle\Entity\Salle")
	*/
	private $salles;
	
	
	/**
	* @ORM\ManyToMany(targetEntity="PersonnesBundle\Entity\Professeur")
	* @ORM\JoinColumn(nullable=true)
	*/
	private $professeurs;
	
	
	/**
	* @ORM\ManyToMany(targetEntity="PersonnesBundle\Entity\ProfesseurAssociation")
	* @ORM\JoinColumn(nullable=true)
	*/
	private $professeursAsso;
	
	
	/**
	* @ORM\ManyToMany(targetEntity="PersonnesBundle\Entity\Personne")
	*/
	private $stagiaires;
    
    
    /**
    * Constructor
    */
    public function __construct()
    {
        $this->saison = new \Doctrine\Common\Collections\ArrayCollection();
        $this->danse = new \Doctrine\Common\Collections\ArrayCollection();
        $this->niveauxDanse = new \Doctrine\Common\Collections\ArrayCollection();
        $this->salle = new \Doctrine\Common\Collections\ArrayCollection();
        $this->professeur = new \Doctrine\Common\Collections\ArrayCollection();
        $this->stagiaires = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set titreStage
     *
     * @param string $titreStage
     * @return Stage
     */
    public function setTitreStage($titreStage)
    {
        $this->titreStage = $titreStage;

        return $this;
    }

    /**
     * Get titreStage
     *
     * @return string 
     */
    public function getTitreStage()
    {
        return $this->titreStage;
    }

    /**
     * Set dateStage
     *
     * @param \DateTime $dateStage
     * @return Stage
     */
    public function setDateStage($dateStage)
    {
        $this->dateStage = $dateStage;

        return $this;
    }

    /**
     * Get dateStage
     *
     * @return \DateTime 
     */
    public function getDateStage()
    {
        return $this->dateStage;
    }

    /**
     * Set heureDebutStage
     *
     * @param \DateTime $heureDebutStage
     * @return Stage
     */
    public function setHeureDebutStage($heureDebutStage)
    {
        $this->heureDebutStage = $heureDebutStage;

        return $this;
    }

    /**
     * Get heureDebutStage
     *
     * @return \DateTime 
     */
    public function getHeureDebutStage()
    {
        return $this->heureDebutStage;
    }

    /**
     * Set heureFinStage
     *
     * @param \DateTime $heureFinStage
     * @return Stage
     */
    public function setHeureFinStage($heureFinStage)
    {
        $this->heureFinStage = $heureFinStage;

        return $this;
    }

    /**
     * Get heureFinStage
     *
     * @return \DateTime 
     */
    public function getHeureFinStage()
    {
        return $this->heureFinStage;
    }

    /**
     * Set capaciteStage
     *
     * @param integer $capaciteStage
     * @return Stage
     */
    public function setCapaciteStage($capaciteStage)
    {
        $this->capaciteStage = $capaciteStage;

        return $this;
    }

    /**
     * Get capaciteStage
     *
     * @return integer 
     */
    public function getCapaciteStage()
    {
        return $this->capaciteStage;
    }

    /**
     * Set montantPreinscription
     *
     * @param integer $montantPreinscription
     * @return Stage
     */
    public function setMontantPreinscription($montantPreinscription)
    {
        $this->montantPreinscription = $montantPreinscription;

        return $this;
    }

    /**
     * Get montantPreinscription
     *
     * @return integer 
     */
    public function getMontantPreinscription()
    {
        return $this->montantPreinscription;
    }

    /**
     * Set delaiPreinscription
     *
     * @param \DateTime $delaiPreinscription
     * @return Stage
     */
    public function setDelaiPreinscription($delaiPreinscription)
    {
        $this->delaiPreinscription = $delaiPreinscription;

        return $this;
    }

    /**
     * Get delaiPreinscription
     *
     * @return \DateTime 
     */
    public function getDelaiPreinscription()
    {
        return $this->delaiPreinscription;
    }

    /**
     * Set tarifStage
     *
     * @param integer $tarifStage
     * @return Stage
     */
    public function setTarifStage($tarifStage)
    {
        $this->tarifStage = $tarifStage;

        return $this;
    }

    /**
     * Get tarifStage
     *
     * @return integer 
     */
    public function getTarifStage()
    {
        return $this->tarifStage;
    }

    /**
     * Set descriptifStage
     *
     * @param string $descriptifStage
     * @return Stage
     */
    public function setDescriptifStage($descriptifStage)
    {
        $this->descriptifStage = $descriptifStage;

        return $this;
    }

    /**
     * Get descriptifStage
     *
     * @return string 
     */
    public function getDescriptifStage()
    {
        return $this->descriptifStage;
    }

    /**
     * Add saison
     *
     * @param \SaisonsBundle\Entity\Saison $saison
     * @return Stage
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
     * Add danse
     *
     * @param \SaisonsBundle\Entity\Danse $danse
     * @return Stage
     */
    public function addDanse(\SaisonsBundle\Entity\Danse $danse)
    {
        $this->danse[] = $danse;

        return $this;
    }

    /**
     * Remove danse
     *
     * @param \SaisonsBundle\Entity\Danse $danse
     */
    public function removeDanse(\SaisonsBundle\Entity\Danse $danse)
    {
        $this->danse->removeElement($danse);
    }

    /**
     * Get danse
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getDanse()
    {
        return $this->danse;
    }

    /**
     * Add niveauxDanse
     *
     * @param \SaisonsBundle\Entity\NiveauDanse $niveauxDanse
     * @return Stage
     */
    public function addNiveauxDanse(\SaisonsBundle\Entity\NiveauDanse $niveauxDanse)
    {
        $this->niveauxDanse[] = $niveauxDanse;

        return $this;
    }

    /**
     * Remove niveauxDanse
     *
     * @param \SaisonsBundle\Entity\NiveauDanse $niveauxDanse
     */
    public function removeNiveauxDanse(\SaisonsBundle\Entity\NiveauDanse $niveauxDanse)
    {
        $this->niveauxDanse->removeElement($niveauxDanse);
    }

    /**
     * Get niveauxDanse
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getNiveauxDanse()
    {
        return $this->niveauxDanse;
    }

    /**
     * Add salle
     *
     * @param \SaisonsBundle\Entity\Salle $salle
     * @return Stage
     */
    public function addSalle(\SaisonsBundle\Entity\Salle $salle)
    {
        $this->salle[] = $salle;

        return $this;
    }

    /**
     * Remove salle
     *
     * @param \SaisonsBundle\Entity\Salle $salle
     */
    public function removeSalle(\SaisonsBundle\Entity\Salle $salle)
    {
        $this->salle->removeElement($salle);
    }

    /**
     * Get salle
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSalle()
    {
        return $this->salle;
    }

    /**
     * Add professeur
     *
     * @param \PersonnesBundle\Entity\Professeur $professeur
     * @return Stage
     */

    public function setProfesseurs(\PersonnesBundle\Entity\Professeur $professeur = null)
	{
        $this->professeurs = $professeur;
    }

    /**
    * Get professeur
    *
    * @return \Doctrine\Common\Collections\Collection 
    */
    public function getProfesseurs(){
		return $this->professeurs;
    }

    /**
     * Add stagiaires
     *
     * @param \PersonnesBundle\Entity\Personne $stagiaires
     * @return Stage
     */
    public function addStagiaire(\PersonnesBundle\Entity\Personne $stagiaires)
    {
        $this->stagiaires[] = $stagiaires;

        return $this;
    }

    /**
     * Remove stagiaires
     *
     * @param \PersonnesBundle\Entity\Personne $stagiaires
     */
    public function removeStagiaire(\PersonnesBundle\Entity\Personne $stagiaires)
    {
        $this->stagiaires->removeElement($stagiaires);
    }

    /**
     * Get stagiaires
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getStagiaires()
    {
        return $this->stagiaires;
    }

    /**
     * Set saison
     *
     * @param \SaisonsBundle\Entity\Saison $saison
     * @return Stage
     */
    public function setSaison(\SaisonsBundle\Entity\Saison $saison)
    {
        $this->saison = $saison;

        return $this;
    }

    /**
     * Set danse
     *
     * @param \SaisonsBundle\Entity\Danse $danse
     * @return Stage
     */
    public function setDanse(\SaisonsBundle\Entity\Danse $danse)
    {
        $this->danse = $danse;

        return $this;
    }
    

    /**
     * Add professeurs
     *
     * @param \PersonnesBundle\Entity\Professeur $professeurs
     * @return Stage
     */
    public function addProfesseur(\PersonnesBundle\Entity\Professeur $professeurs)
    {
        $this->professeurs[] = $professeurs;

        return $this;
    }

    /**
     * Remove professeurs
     *
     * @param \PersonnesBundle\Entity\Professeur $professeurs
     */
    public function removeProfesseur(\PersonnesBundle\Entity\Professeur $professeurs)
    {
        $this->professeurs->removeElement($professeurs);
    }

    /**
     * Get salles
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSalles()
    {
        return $this->salles;
    }

    /**
     * Add professeursAsso
     *
     * @param \PersonnesBundle\Entity\ProfesseurAssociation $professeursAsso
     * @return Stage
     */
    public function addProfesseursAsso(\PersonnesBundle\Entity\ProfesseurAssociation $professeursAsso)
    {
        $this->professeursAsso[] = $professeursAsso;

        return $this;
    }

    /**
     * Remove professeursAsso
     *
     * @param \PersonnesBundle\Entity\ProfesseurAssociation $professeursAsso
     */
    public function removeProfesseursAsso(\PersonnesBundle\Entity\ProfesseurAssociation $professeursAsso)
    {
        $this->professeursAsso->removeElement($professeursAsso);
    }

    /**
     * Get professeursAsso
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getProfesseursAsso()
    {
        return $this->professeursAsso;
    }
}
