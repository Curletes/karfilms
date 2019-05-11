<?php

namespace KarfilmsBundle\Entity;

/**
 * Valoracion
 */
class Valoracion
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var \KarfilmsBundle\Entity\Sugerencia
     */
    private $idSugerencia;

    /**
     * @var \KarfilmsBundle\Entity\Usuario
     */
    private $idUsuario;


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
     * Set idSugerencia
     *
     * @param \KarfilmsBundle\Entity\Sugerencia $idSugerencia
     *
     * @return Valoracion
     */
    public function setIdSugerencia(\KarfilmsBundle\Entity\Sugerencia $idSugerencia = null)
    {
        $this->idSugerencia = $idSugerencia;

        return $this;
    }

    /**
     * Get idSugerencia
     *
     * @return \KarfilmsBundle\Entity\Sugerencia
     */
    public function getIdSugerencia()
    {
        return $this->idSugerencia;
    }

    /**
     * Set idUsuario
     *
     * @param \KarfilmsBundle\Entity\Usuario $idUsuario
     *
     * @return Valoracion
     */
    public function setIdUsuario(\KarfilmsBundle\Entity\Usuario $idUsuario = null)
    {
        $this->idUsuario = $idUsuario;

        return $this;
    }

    /**
     * Get idUsuario
     *
     * @return \KarfilmsBundle\Entity\Usuario
     */
    public function getIdUsuario()
    {
        return $this->idUsuario;
    }
}

