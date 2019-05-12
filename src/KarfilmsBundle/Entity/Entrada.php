<?php

namespace KarfilmsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Entrada
 *
 * @ORM\Table(name="entradas", indexes={@ORM\Index(name="fk_id_sesion_idx", columns={"id_sesion"}), @ORM\Index(name="fk_id_user_idx", columns={"id_usuario"}), @ORM\Index(name="fk_asiento_idx", columns={"id_asiento"})})
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

    /**
     * @var \KarfilmsBundle\Entity\Asiento
     *
     * @ORM\ManyToOne(targetEntity="KarfilmsBundle\Entity\Asiento")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_asiento", referencedColumnName="id")
     * })
     */
    private $idAsiento;

    function getId() {
        return $this->id;
    }

    function getIdSesion(): \KarfilmsBundle\Entity\Sesion {
        return $this->idSesion;
    }

    function getIdUsuario(): \KarfilmsBundle\Entity\Usuario {
        return $this->idUsuario;
    }

    function getIdAsiento(): \KarfilmsBundle\Entity\Asiento {
        return $this->idAsiento;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setIdSesion(\KarfilmsBundle\Entity\Sesion $idSesion) {
        $this->idSesion = $idSesion;
    }

    function setIdUsuario(\KarfilmsBundle\Entity\Usuario $idUsuario) {
        $this->idUsuario = $idUsuario;
    }

    function setIdAsiento(\KarfilmsBundle\Entity\Asiento $idAsiento) {
        $this->idAsiento = $idAsiento;
    }


}

