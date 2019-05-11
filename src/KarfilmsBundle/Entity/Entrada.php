<?php

namespace KarfilmsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Entrada
 *
 * @ORM\Table(name="entradas", indexes={@ORM\Index(name="fk_id_sesion_idx", columns={"id_sesion"}), @ORM\Index(name="fk_id_user_idx", columns={"id_usuario"})})
 * @ORM\Entity
 */
class Entrada
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
     * @var integer
     *
     * @ORM\Column(name="fila", type="integer", nullable=false)
     */
    private $fila;

    /**
     * @var integer
     *
     * @ORM\Column(name="butaca", type="integer", nullable=false)
     */
    private $butaca;

    /**
     * @var \KarfilmsBundle\Entity\Sesion
     *
     * @ORM\ManyToOne(targetEntity="KarfilmsBundle\Entity\Sesion")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_sesion", referencedColumnName="id")
     * })
     */
    private $idSesion;

    /**
     * @var \KarfilmsBundle\Entity\Usuario
     *
     * @ORM\ManyToOne(targetEntity="KarfilmsBundle\Entity\Usuario")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_usuario", referencedColumnName="id")
     * })
     */
    private $idUsuario;

    function getId() {
        return $this->id;
    }

    function getFila() {
        return $this->fila;
    }

    function getButaca() {
        return $this->butaca;
    }

    function getIdSesion(): \KarfilmsBundle\Entity\Sesion {
        return $this->idSesion;
    }

    function getIdUsuario(): \KarfilmsBundle\Entity\Usuario {
        return $this->idUsuario;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setFila($fila) {
        $this->fila = $fila;
    }

    function setButaca($butaca) {
        $this->butaca = $butaca;
    }

    function setIdSesion(\KarfilmsBundle\Entity\Sesion $idSesion) {
        $this->idSesion = $idSesion;
    }

    function setIdUsuario(\KarfilmsBundle\Entity\Usuario $idUsuario) {
        $this->idUsuario = $idUsuario;
    }


}

