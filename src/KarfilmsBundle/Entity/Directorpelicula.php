<?php

namespace KarfilmsBundle\Entity;

/**
 * Directorpelicula
 */
class Directorpelicula
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var \KarfilmsBundle\Entity\Director
     */
    private $idDirector;

    /**
     * @var \KarfilmsBundle\Entity\Pelicula
     */
    private $idPelicula;


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
     * Set idDirector
     *
     * @param \KarfilmsBundle\Entity\Director $idDirector
     *
     * @return Directorpelicula
     */
    public function setIdDirector(\KarfilmsBundle\Entity\Director $idDirector = null)
    {
        $this->idDirector = $idDirector;

        return $this;
    }

    /**
     * Get idDirector
     *
     * @return \KarfilmsBundle\Entity\Director
     */
    public function getIdDirector()
    {
        return $this->idDirector;
    }

    /**
     * Set idPelicula
     *
     * @param \KarfilmsBundle\Entity\Pelicula $idPelicula
     *
     * @return Directorpelicula
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
}

