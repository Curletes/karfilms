<?php

namespace KarfilmsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Pelicula
 *
 * @ORM\Table(name="peliculas", indexes={@ORM\Index(name="fk_edad_idx", columns={"id_edad"})})
 * @ORM\Entity(repositoryClass="KarfilmsBundle\Repository\ProductRepository")
 */
class Pelicula {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="titulo", type="text", length=65535, nullable=false)
     */
    private $titulo;

    /**
     * @var string
     *
     * @ORM\Column(name="sinopsis", type="text", length=65535, nullable=false)
     */
    private $sinopsis;

    /**
     * @var string
     *
     * @ORM\Column(name="cartel", type="string", length=255, nullable=false)
     */
    private $cartel;

    /**
     * @var string
     *
     * @ORM\Column(name="trailer", type="string", length=255, nullable=false)
     */
    private $trailer;

    /**
     * @var integer
     *
     * @ORM\Column(name="duracion", type="integer", nullable=false)
     */
    private $duracion;

    /**
     * @var \KarfilmsBundle\Entity\Edad
     *
     * @ORM\ManyToOne(targetEntity="KarfilmsBundle\Entity\Edad")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_edad", referencedColumnName="id")
     * })
     */
    private $idEdad;
    protected $Generopelicula;
    protected $Actorpelicula;
    protected $Directorpelicula;
    protected $sesion;

    public function __construct() {
        $this->Generopelicula = new ArrayCollection();
        $this->Actorpelicula = new ArrayCollection();
        $this->Directorpelicula = new ArrayCollection();
        $this->sesion = new ArrayCollection();
    }

    public function __toString() {
        return $this->getTitulo();
    }

    function getId() {
        return $this->id;
    }

    function getTitulo() {
        return $this->titulo;
    }

    function getSinopsis() {
        return $this->sinopsis;
    }

    function getCartel() {
        return $this->cartel;
    }

    function getTrailer() {
        return $this->trailer;
    }

    function getDuracion() {
        return $this->duracion;
    }

    function getIdEdad(): \KarfilmsBundle\Entity\Edad {
        return $this->idEdad;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setTitulo($titulo) {
        $this->titulo = $titulo;
    }

    function setSinopsis($sinopsis) {
        $this->sinopsis = $sinopsis;
    }

    function setCartel($cartel) {
        $this->cartel = $cartel;
    }

    function setTrailer($trailer) {
        $this->trailer = $trailer;
    }

    function setDuracion($duracion) {
        $this->duracion = $duracion;
    }

    function setIdEdad(\KarfilmsBundle\Entity\Edad $idEdad) {
        $this->idEdad = $idEdad;
    }

    public function addGeneropelicula(\KarfilmsBundle\Entity\Genero $genero) {
        $this->Generopelicula[] = $genero;

        return $this;
    }

    public function getGeneropelicula() {
        return $this->Generopelicula;
    }

    public function addActorpelicula(\KarfilmsBundle\Entity\Actor $actor) {
        $this->Actorpelicula[] = $actor;

        return $this;
    }

    public function getActorpelicula() {
        return $this->Actorpelicula;
    }

    public function addDirectorpelicula(\KarfilmsBundle\Entity\Director $director) {
        $this->Directorpelicula[] = $director;

        return $this;
    }

    public function getDirectorpelicula() {
        return $this->Directorpelicula;
    }

    public function getSesiones() {
        return $this->sesion;
    }

}
