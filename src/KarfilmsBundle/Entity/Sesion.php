<?php

namespace KarfilmsBundle\Entity;

/**
 * Sesion
 */
class Sesion
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var \DateTime
     */
    private $fecha;

    /**
     * @var \DateTime
     */
    private $hora;

    /**
     * @var \KarfilmsBundle\Entity\Pelicula
     */
    private $idPelicula;

    /**
     * @var \KarfilmsBundle\Entity\Sala
     */
    private $idSala;


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
     * Set fecha
     *
     * @param \DateTime $fecha
     *
     * @return Sesion
     */
    public function setFecha($fecha)
    {
        $this->fecha = $fecha;

        return $this;
    }

    /**
     * Get fecha
     *
     * @return \DateTime
     */
    public function getFecha()
    {
        return $this->fecha;
    }

    /**
     * Set hora
     *
     * @param \DateTime $hora
     *
     * @return Sesion
     */
    public function setHora($hora)
    {
        $this->hora = $hora;

        return $this;
    }

    /**
     * Get hora
     *
     * @return \DateTime
     */
    public function getHora()
    {
        return $this->hora;
    }

    /**
     * Set idPelicula
     *
     * @param \KarfilmsBundle\Entity\Pelicula $idPelicula
     *
     * @return Sesion
     */
    public function setIdPelicula(\KarfilmsBundle\Entity\Pelicula $idPelicula = null)
    {
        $this->idPelicula = $idPelicula;

        return $this;
    }

    /**
     * Get idPelicula
     *
     * @return \KarfilmsBundle\Entity\Pelicula
     */
    public function getIdPelicula()
    {
        return $this->idPelicula;
    }

    /**
     * Set idSala
     *
     * @param \KarfilmsBundle\Entity\Sala $idSala
     *
     * @return Sesion
     */
    public function setIdSala(\KarfilmsBundle\Entity\Sala $idSala = null)
    {
        $this->idSala = $idSala;

        return $this;
    }

    /**
     * Get idSala
     *
     * @return \KarfilmsBundle\Entity\Sala
     */
    public function getIdSala()
    {
        return $this->idSala;
    }
}

