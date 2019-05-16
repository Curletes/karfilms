<?php

namespace KarfilmsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Sesion
 *
 * @ORM\Table(name="sesiones", uniqueConstraints={@ORM\UniqueConstraint(name="fk_horarios", columns={"id_sala", "horarios"})}, indexes={@ORM\Index(name="fk_id_sala_idx", columns={"id_sala"}), @ORM\Index(name="fk_id_pelicula_idx", columns={"id_pelicula"})})
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
     * @ORM\Column(name="horarios", type="datetime", nullable=false)
     */
    private $horarios = 'CURRENT_TIMESTAMP';

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

    function getHorarios(): \DateTime {
        return $this->horarios;
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

    function setHorarios(\DateTime $horarios) {
        $this->horarios = $horarios;
    }

    function setIdPelicula(\KarfilmsBundle\Entity\Pelicula $idPelicula) {
        $this->idPelicula = $idPelicula;
    }

    function setIdSala(\KarfilmsBundle\Entity\Sala $idSala) {
        $this->idSala = $idSala;
    }



}

