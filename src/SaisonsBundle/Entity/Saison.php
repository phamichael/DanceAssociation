<?php

namespace SaisonsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Saison
 *
 * @ORM\Table(name="saison")
 * @ORM\Entity(repositoryClass="SaisonsBundle\Repository\SaisonRepository")
 */
class Saison{
	
	/**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
	
	
	
	/**
     * @var int
     * 
     * @ORM\Column(name="anneeDebutSaison", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $anneeDebutSaison;
	
	
    /**
     * @var int
     *
     * @ORM\Column(name="tarifSaison", type="integer")
     * @ORM\JoinColumn(nullable=true)
     */
    private $tarifSaison;

	
	// ****[ LES CLES :
	
	
	/**
	* @ORM\ManyToMany(targetEntity="CoursBundle\Entity\Cours", cascade={"persist"})
    * @ORM\JoinColumn(nullable=true)
	*/
	private $cours;
			
	/**
	* @ORM\OneToMany(targetEntity="StagesBundle\Entity\Stage", mappedBy="saison", cascade={"remove"})
	*/
	private $stages;
	
	/**
	* @ORM\OneToMany(targetEntity="SoireesBundle\Entity\Soiree", mappedBy="saison", cascade={"remove"})
	*/
	private $soirees;
	
	
	/**
	* @ORM\ManyToMany(targetEntity="SaisonsBundle\Entity\TypeForfait", cascade={"persist"})
	*/
	private $typesForfait;
	
	
	/**
	* @ORM\ManyToMany(targetEntity="SaisonsBundle\Entity\Salle", cascade={"persist"})
    * @ORM\JoinColumn(nullable=true)
	*/
	private $salles;
	
	/**
	* @ORM\ManyToMany(targetEntity="SaisonsBundle\Entity\Danse", cascade={"persist"})
    * @ORM\JoinColumn(nullable=true)
	*/
	private $danses;
	
	/**
	* @ORM\ManyToMany(targetEntity="PersonnesBundle\Entity\Professeur", cascade={"persist"})
    * @ORM\JoinColumn(nullable=true)
	*/
	private $professeurs;

    /**
     * Set anneeDebutSaison
     *
     * @param integer $anneeDebutSaison
     * @return Saison
     */
    public function setAnneeDebutSaison($anneeDebutSaison)
    {
        $this->anneeDebutSaison = $anneeDebutSaison;

        return $this;
    }

    /**
     * Get anneeDebutSaison
     *
     * @return integer 
     */
    public function getAnneeDebutSaison()
    {
        return $this->anneeDebutSaison;
    }

    /**
     * Set tarifSaison
     *
     * @param integer $tarifSaison
     * @return Saison
     */
    public function setTarifSaison($tarifSaison)
    {
        $this->tarifSaison = $tarifSaison;

        return $this;
    }

    /**
     * Get tarifSaison
     *
     * @return integer 
     */
    public function getTarifSaison()
    {
        return $this->tarifSaison;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->cours = new \Doctrine\Common\Collections\ArrayCollection();
        $this->stages = new \Doctrine\Common\Collections\ArrayCollection();
        $this->soirees = new \Doctrine\Common\Collections\ArrayCollection();
        $this->salles = new \Doctrine\Common\Collections\ArrayCollection();
        $this->danses = new \Doctrine\Common\Collections\ArrayCollection();
        $this->professeurs = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add cours
     *
     * @param \CoursBundle\Entity\Cours $cours
     * @return Saison
     */
    public function addCour(\CoursBundle\Entity\Cours $cours)
    {
        $this->cours[] = $cours;

        return $this;
    }

    /**
     * Remove cours
     *
     * @param \CoursBundle\Entity\Cours $cours
     */
    public function removeCour(\CoursBundle\Entity\Cours $cours)
    {
        $this->cours->removeElement($cours);
    }

    /**
     * Get cours
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCours()
    {
        return $this->cours;
    }

    /**
     * Add stages
     *
     * @param \StagesBundle\Entity\Stage $stages
     * @return Saison
     */
    public function addStage(\StagesBundle\Entity\Stage $stages)
    {
        $this->stages[] = $stages;

        return $this;
    }

    /**
     * Remove stages
     *
     * @param \StagesBundle\Entity\Stage $stages
     */
    public function removeStage(\StagesBundle\Entity\Stage $stages)
    {
        $this->stages->removeElement($stages);
    }

    /**
     * Get stages
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getStages()
    {
        return $this->stages;
    }

    /**
     * Add soirees
     *
     * @param \SoireesBundle\Entity\Soiree $soirees
     * @return Saison
     */
    public function addSoiree(\SoireesBundle\Entity\Soiree $soirees)
    {
        $this->soirees[] = $soirees;

        return $this;
    }

    /**
     * Remove soirees
     *
     * @param \SoireesBundle\Entity\Soiree $soirees
     */
    public function removeSoiree(\SoireesBundle\Entity\Soiree $soirees)
    {
        $this->soirees->removeElement($soirees);
    }

    /**
     * Get soirees
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSoirees()
    {
        return $this->soirees;
    }

    /**
     * Add salles
     *
     * @param \SaisonsBundle\Entity\Salle $salles
     * @return Saison
     */
    public function addSalle(\SaisonsBundle\Entity\Salle $salles)
    {
        $this->salles[] = $salles;

        return $this;
    }

    /**
     * Remove salles
     *
     * @param \SaisonsBundle\Entity\Salle $salles
     */
    public function removeSalle(\SaisonsBundle\Entity\Salle $salles)
    {
        $this->salles->removeElement($salles);
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
     * Add danses
     *
     * @param \SaisonsBundle\Entity\Danse $danses
     * @return Saison
     */
    public function addDanse(\SaisonsBundle\Entity\Danse $danses)
    {
        $this->danses[] = $danses;

        return $this;
    }

    /**
     * Remove danses
     *
     * @param \SaisonsBundle\Entity\Danse $danses
     */
    public function removeDanse(\SaisonsBundle\Entity\Danse $danses)
    {
        $this->danses->removeElement($danses);
    }

    /**
     * Get danses
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getDanses()
    {
        return $this->danses;
    }

    /**
     * Add professeurs
     *
     * @param \PersonnesBundle\Entity\Professeur $professeurs
     * @return Saison
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
     * Get professeurs
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getProfesseurs()
    {
        return $this->professeurs;
    }

    /**
     * Add typesForfait
     *
     * @param \SaisonsBundle\Entity\TypeForfait $typesForfait
     * @return Saison
     */
    public function addTypesForfait(\SaisonsBundle\Entity\TypeForfait $typesForfait)
    {
        $this->typesForfait[] = $typesForfait;

        return $this;
    }

    /**
     * Remove typesForfait
     *
     * @param \SaisonsBundle\Entity\TypeForfait $typesForfait
     */
    public function removeTypesForfait(\SaisonsBundle\Entity\TypeForfait $typesForfait)
    {
        $this->typesForfait->removeElement($typesForfait);
    }

    /**
     * Get typesForfait
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTypesForfait()
    {
        return $this->typesForfait;
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
}
