<?php

namespace KarfilmsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Sesion
 *
 * @ORM\Table(name="sesiones", indexes={@ORM\Index(name="fk_id_sala_idx", columns={"id_sala"}), @ORM\Index(name="fk_id_pelicula_idx", columns={"id_pelicula"})})
 * @ORM\Entity
 */
class Sesion
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
     * @var \DateTime
     *
     * @ORM\Column(name="fecha", type="date", nullable=false)
     */
    private $fecha;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="hora", type="time", nullable=false)
     */
    private $hora;

    /**
     * @var \KarfilmsBundle\Entity\Pelicula
     *
     * @ORM\ManyToOne(targetEntity="KarfilmsBundle\Entity\Pelicula")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_pelicula", referencedColumnName="id")
     * })
     */
    private $idPelicula;

    /**
     * @var \KarfilmsBundle\Entity\Sala
     *
     * @ORM\ManyToOne(targetEntity="KarfilmsBundle\Entity\Sala")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_sala", referencedColumnName="id")
     * })
     */
    private $idSala;
    function getId() {
        return $this->id;
    }

    function getFecha(): \DateTime {
        return $this->fecha;
    }

    function getHora(): \DateTime {
        return $this->hora;
    }

    function getIdPelicula(): \KarfilmsBundle\Entity\Pelicula {
        return $this->idPelicula;
    }

    function getIdSala(): \KarfilmsBundle\Entity\Sala {
        return $this->idSala;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setFecha(\DateTime $fecha) {
        $this->fecha = $fecha;
    }

    function setHora(\DateTime $hora) {
        $this->hora = $hora;
    }

    function setIdPelicula(\KarfilmsBundle\Entity\Pelicula $idPelicula) {
        $this->idPelicula = $idPelicula;
    }

    function setIdSala(\KarfilmsBundle\Entity\Sala $idSala) {
        $this->idSala = $idSala;
    }



}

