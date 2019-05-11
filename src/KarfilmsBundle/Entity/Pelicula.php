<?php

namespace KarfilmsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Pelicula
 *
 * @ORM\Table(name="peliculas")
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
     * @var integer
     *
     * @ORM\Column(name="edad", type="integer", nullable=false)
     */
    private $edad;

    /**
     * @var string
     *
     * @ORM\Column(name="sinopsis", type="text", length=65535, nullable=false)
     */
    private $sinopsis;

    /**
     * @var string
     *
     * @ORM\Column(name="cartel", type="string", length=45, nullable=false)
     */
    private $cartel;

    /**
     * @var string
     *
     * @ORM\Column(name="trailer", type="string", length=45, nullable=false)
     */
    private $trailer;

    /**
     * @var integer
     *
     * @ORM\Column(name="duracion", type="integer", nullable=false)
     */
    private $duracion;

    function getId() {
        return $this->id;
    }

    function getTitulo() {
        return $this->titulo;
    }

    function getEdad() {
        return $this->edad;
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

    function setId($id) {
        $this->id = $id;
    }

    function setTitulo($titulo) {
        $this->titulo = $titulo;
    }

    function setEdad($edad) {
        $this->edad = $edad;
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


}

