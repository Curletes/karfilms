<?php

namespace KarfilmsBundle\Entity;

/**
 * Entrada
 */
class Entrada
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $fila;

    /**
     * @var integer
     */
    private $butaca;

    /**
     * @var \KarfilmsBundle\Entity\Sesion
     */
    private $idSesion;

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
     * Set fila
     *
     * @param integer $fila
     *
     * @return Entrada
     */
    public function setFila($fila)
    {
        $this->fila = $fila;

        return $this;
    }

    /**
     * Get fila
     *
     * @return integer
     */
    public function getFila()
    {
        return $this->fila;
    }

    /**
     * Set butaca
     *
     * @param integer $butaca
     *
     * @return Entrada
     */
    public function setButaca($butaca)
    {
        $this->butaca = $butaca;

        return $this;
    }

    /**
     * Get butaca
     *
     * @return integer
     */
    public function getButaca()
    {
        return $this->butaca;
    }

    /**
     * Set idSesion
     *
     * @param \KarfilmsBundle\Entity\Sesion $idSesion
     *
     * @return Entrada
     */
    public function setIdSesion(\KarfilmsBundle\Entity\Sesion $idSesion = null)
    {
        $this->idSesion = $idSesion;

        return $this;
    }

    /**
     * Get idSesion
     *
     * @return \KarfilmsBundle\Entity\Sesion
     */
    public function getIdSesion()
    {
        return $this->idSesion;
    }

    /**
     * Set idUsuario
     *
     * @param \KarfilmsBundle\Entity\Usuario $idUsuario
     *
     * @return Entrada
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

