<?php

namespace SaisonsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Danse
 *
 * @ORM\Table(name="danse")
 * @ORM\Entity(repositoryClass="SaisonsBundle\Repository\DanseRepository")
 */
class Danse
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
     * @ORM\Column(name="nomDanse", type="string", length=255)
     */
    private $nomDanse;


	/**
     * @var string
     *
     * @ORM\Column(name="descriptifDanse", type="text")
     */
    private $descriptifDanse;

    /**
     * @var string
     *
     * @ORM\Column(name="nomImage", type="text")
     * @ORM\JoinColumn(nullable=true)
     */
    private $nomImage;

    private $file; 


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
     * Set nomDanse
     *
     * @param string $nomDanse
     * @return Danse
     */
    public function setNomDanse($nomDanse)
    {
        $this->nomDanse = $nomDanse;

        return $this;
    }

    /**
     * Get nomDanse
     *
     * @return string 
     */
    public function getNomDanse()
    {
        return $this->nomDanse;
    }

    /**
     * Set descriptifDanse
     *
     * @param string $descriptifDanse
     * @return Danse
     */
    public function setDescriptifDanse($descriptifDanse)
    {
        $this->descriptifDanse = $descriptifDanse;

        return $this;
    }

    /**
     * Get descriptifDanse
     *
     * @return string 
     */
    public function getDescriptifDanse()
    {
        return $this->descriptifDanse;
    }

    /**
     * Set nomImage
     *
     * @param string $nomImage
     * @return Danse
     */
    public function setNomImage($nomImage)
    {
        $this->nomImage = $nomImage;

        return $this;
    }

    /**
     * Get nomImage
     *
     * @return string 
     */
    public function getNomImage()
    {
        return $this->nomImage;
    }

  public function getFile()
  {
    return $this->file;
  }

  public function setFile(UploadedFile $file = null)
  {
    $this->file = $file;
  }


public function upload()
  {
    // Si jamais il n'y a pas de fichier (champ facultatif), on ne fait rien
    if (null === $this->file) {
      return;
    }

    // On récupère le nom original du fichier de l'internaute
    $name = $this->file->getClientOriginalName();

    // On déplace le fichier envoyé dans le répertoire de notre choix
    $this->file->move($this->getUploadRootDir(), $name);

    // On sauvegarde le nom de fichier dans notre attribut $url
    $this->nomImage = $name;
  }

  public function getUploadDir()
  {
    // On retourne le chemin relatif vers l'image pour un navigateur (relatif au répertoire /web donc)
    return 'img';
  }

  protected function getUploadRootDir()
  {
    // On retourne le chemin relatif vers l'image pour notre code PHP
   // return __DIR__;
    return __DIR__.'/../../../web/'.$this->getUploadDir();
  }

}
