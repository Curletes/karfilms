<?php

namespace KarfilmsBundle\Entity;

/**
 * Actorpelicula
 */
class Actorpelicula
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var \KarfilmsBundle\Entity\Actor
     */
    private $idActor;

    /**
     * @var \KarfilmsBundle\Entity\Peliculas
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
     * Set idActor
     *
     * @param \KarfilmsBundle\Entity\Actor $idActor
     *
     * @return Actorpelicula
     */
    public function setIdActor(\KarfilmsBundle\Entity\Actor $idActor = null)
    {
        $this->idActor = $idActor;

        return $this;
    }

    /**
     * Get idActor
     *
     * @return \KarfilmsBundle\Entity\Actor
     */
    public function getIdActor()
    {
        return $this->idActor;
    }

    /**
     * Set idPelicula
     *
     * @param \KarfilmsBundle\Entity\Peliculas $idPelicula
     *
     * @return Actorpelicula
     */
    public function setIdPelicula(\KarfilmsBundle\Entity\Peliculas $idPelicula = null)
    {
        $this->idPelicula = $idPelicula;

        return $this;
    }

    /**
     * Get idPelicula
     *
     * @return \KarfilmsBundle\Entity\Peliculas
     */
    public function getIdPelicula()
    {
        return $this->idPelicula;
    }
}

