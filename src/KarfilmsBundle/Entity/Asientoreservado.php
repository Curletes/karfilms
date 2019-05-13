<?php

namespace KarfilmsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Asientoreservado
 *
 * @ORM\Table(name="asientosreservados", indexes={@ORM\Index(name="fk_reserva_idx", columns={"id_reserva"}), @ORM\Index(name="fk_seat_idx", columns={"id_asiento"}), @ORM\Index(name="fk_session_idx", columns={"id_sesion"})})
 * @ORM\Entity
 */
class Asientoreservado
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
     * @var \KarfilmsBundle\Entity\Reserva
     *
     * @ORM\ManyToOne(targetEntity="KarfilmsBundle\Entity\Reserva")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_reserva", referencedColumnName="id")
     * })
     */
    private $idReserva;

    /**
     * @var \KarfilmsBundle\Entity\Asiento
     *
     * @ORM\ManyToOne(targetEntity="KarfilmsBundle\Entity\Asiento")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_asiento", referencedColumnName="id")
     * })
     */
    private $idAsiento;

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

    function getIdReserva(): \KarfilmsBundle\Entity\Reserva {
        return $this->idReserva;
    }

    function getIdAsiento(): \KarfilmsBundle\Entity\Asiento {
        return $this->idAsiento;
    }

    function getIdSesion(): \KarfilmsBundle\Entity\Sesion {
        return $this->idSesion;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setIdReserva(\KarfilmsBundle\Entity\Reserva $idReserva) {
        $this->idReserva = $idReserva;
    }

    function setIdAsiento(\KarfilmsBundle\Entity\Asiento $idAsiento) {
        $this->idAsiento = $idAsiento;
    }

    function setIdSesion(\KarfilmsBundle\Entity\Sesion $idSesion) {
        $this->idSesion = $idSesion;
    }



}

