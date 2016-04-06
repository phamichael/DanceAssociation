<?php

namespace SaisonsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Forfait
 *
 * @ORM\Table(name="forfait")
 * @ORM\Entity(repositoryClass="SaisonsBundle\Repository\ForfaitRepository")
 */
class Forfait
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
     * @ORM\Column(name="modePaiement", type="string", length=255, nullable=True)
     */
    private $modePaiement;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="datePaiement", type="datetime", nullable=True)
     */
    private $datePaiement;

	
    /**
	* @ORM\ManyToOne(targetEntity="PersonnesBundle\Entity\Adherent")
	* @ORM\JoinColumn(nullable=false)
	*/
    private $adherent;
    
    /**
	* @ORM\ManyToOne(targetEntity="SaisonsBundle\Entity\Saison")
	* @ORM\JoinColumn(nullable=false)
	*/
    private $saison;
    
    /**
	* @ORM\ManyToOne(targetEntity="SaisonsBundle\Entity\TypeForfait")
	* @ORM\JoinColumn(nullable=false)
	*/
    private $typeForfait;
	
	
	// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
	// GROSSE RELATION TERNAIRE A GERER AVEC DANSES ET NIVEAUX DANSE !!!
	// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!


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
     * Set modePaiement
     *
     * @param string $modePaiement
     * @return Forfait
     */
    public function setModePaiement($modePaiement)
    {
        $this->modePaiement = $modePaiement;

        return $this;
    }

    /**
     * Get modePaiement
     *
     * @return string 
     */
    public function getModePaiement()
    {
        return $this->modePaiement;
    }

    /**
     * Set datePaiement
     *
     * @param \DateTime $datePaiement
     * @return Forfait
     */
    public function setDatePaiement($datePaiement)
    {
        $this->datePaiement = $datePaiement;

        return $this;
    }

    /**
     * Get datePaiement
     *
     * @return \DateTime 
     */
    public function getDatePaiement()
    {
        return $this->datePaiement;
    }

    /**
     * Set adherent
     *
     * @param \PersonnesBundle\Entity\Adherent $adherent
     * @return Forfait
     */
    public function setAdherent(\PersonnesBundle\Entity\Adherent $adherent)
    {
        $this->adherent = $adherent;

        return $this;
    }

    /**
     * Get adherent
     *
     * @return \PersonnesBundle\Entity\Adherent 
     */
    public function getAdherent()
    {
        return $this->adherent;
    }

    /**
     * Set saison
     *
     * @param \SaisonsBundle\Entity\Saison $saison
     * @return Forfait
     */
    public function setSaison(\SaisonsBundle\Entity\Saison $saison)
    {
        $this->saison = $saison;

        return $this;
    }

    /**
     * Get saison
     *
     * @return \SaisonsBundle\Entity\Saison 
     */
    public function getSaison()
    {
        return $this->saison;
    }

    /**
     * Set typeForfait
     *
     * @param \SaisonsBundle\Entity\TypeForfait $typeForfait
     * @return Forfait
     */
    public function setTypeForfait(\SaisonsBundle\Entity\TypeForfait $typeForfait)
    {
        $this->typeForfait = $typeForfait;

        return $this;
    }

    /**
     * Get typeForfait
     *
     * @return \SaisonsBundle\Entity\TypeForfait 
     */
    public function getTypeForfait()
    {
        return $this->typeForfait;
    }
}
