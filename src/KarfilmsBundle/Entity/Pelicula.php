<?php

namespace KarfilmsBundle\Entity;

/**
 * Pelicula
 */
class Pelicula
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $titulo;

    /**
     * @var integer
     */
    private $edad;

    /**
     * @var string
     */
    private $sinopsis;

    /**
     * @var string
     */
    private $cartel;

    /**
     * @var string
     */
    private $trailer;

    /**
     * @var integer
     */
    private $duracion;


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
     * Set titulo
     *
     * @param string $titulo
     *
     * @return Pelicula
     */
    public function setTitulo($titulo)
    {
        $this->titulo = $titulo;

        return $this;
    }

    /**
     * Get titulo
     *
     * @return string
     */
    public function getTitulo()
    {
        return $this->titulo;
    }

    /**
     * Set edad
     *
     * @param integer $edad
     *
     * @return Pelicula
     */
    public function setEdad($edad)
    {
        $this->edad = $edad;

        return $this;
    }

    /**
     * Get edad
     *
     * @return integer
     */
    public function getEdad()
    {
        return $this->edad;
    }

    /**
     * Set sinopsis
     *
     * @param string $sinopsis
     *
     * @return Pelicula
     */
    public function setSinopsis($sinopsis)
    {
        $this->sinopsis = $sinopsis;

        return $this;
    }

    /**
     * Get sinopsis
     *
     * @return string
     */
    public function getSinopsis()
    {
        return $this->sinopsis;
    }

    /**
     * Set cartel
     *
     * @param string $cartel
     *
     * @return Pelicula
     */
    public function setCartel($cartel)
    {
        $this->cartel = $cartel;

        return $this;
    }

    /**
     * Get cartel
     *
     * @return string
     */
    public function getCartel()
    {
        return $this->cartel;
    }

    /**
     * Set trailer
     *
     * @param string $trailer
     *
     * @return Pelicula
     */
    public function setTrailer($trailer)
    {
        $this->trailer = $trailer;

        return $this;
    }

    /**
     * Get trailer
     *
     * @return string
     */
    public function getTrailer()
    {
        return $this->trailer;
    }

    /**
     * Set duracion
     *
     * @param integer $duracion
     *
     * @return Pelicula
     */
    public function setDuracion($duracion)
    {
        $this->duracion = $duracion;

        return $this;
    }

    /**
     * Get duracion
     *
     * @return integer
     */
    public function getDuracion()
    {
        return $this->duracion;
    }
}

