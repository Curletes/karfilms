<?php

namespace KarfilmsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Reserva
 *
 * @ORM\Table(name="reservas", indexes={@ORM\Index(name="fk_session_idx", columns={"id_sesion"})})
 * @ORM\Entity
 */
class Reserva
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
     * @ORM\Column(name="reservado", type="integer", nullable=false)
     */
    private $reservado = '0';

    /**
     * @var \KarfilmsBundle\Entity\Sesion
     *
     * @ORM\ManyToOne(targetEntity="KarfilmsBundle\Entity\Sesion")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_sesion", referencedColumnName="id")
     * })
     */
    private $idSesion;

    function getId() {
        return $this->id;
    }

    function getReservado() {
        return $this->reservado;
    }

    function getIdSesion(): \KarfilmsBundle\Entity\Sesion {
        return $this->idSesion;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setReservado($reservado) {
        $this->reservado = $reservado;
    }

    function setIdSesion(\KarfilmsBundle\Entity\Sesion $idSesion) {
        $this->idSesion = $idSesion;
    }


}

