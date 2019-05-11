<?php

namespace KarfilmsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Pelicula
 *
 * @ORM\Table(name="peliculas", indexes={@ORM\Index(name="fk_edad_idx", columns={"id_edad"})})
 * @ORM\Entity
 */
class Pelicula
{
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


}

