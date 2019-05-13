<?php

namespace KarfilmsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Actorpelicula
 *
 * @ORM\Table(name="actorespeliculas", indexes={@ORM\Index(name="fk_id_actor_idx", columns={"id_actor"}), @ORM\Index(name="fk_id_pelicula_idx", columns={"id_pelicula"})})
 * @ORM\Entity
 */
class Actorpelicula
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
     * @var \KarfilmsBundle\Entity\Pelicula
     *
     * @ORM\ManyToOne(targetEntity="KarfilmsBundle\Entity\Pelicula")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_pelicula", referencedColumnName="id")
     * })
     */
    private $idPelicula;

    /**
     * @var \KarfilmsBundle\Entity\Actor
     *
     * @ORM\ManyToOne(targetEntity="KarfilmsBundle\Entity\Actor")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_actor", referencedColumnName="id")
     * })
     */
    private $idActor;

    function getId() {
        return $this->id;
    }

    function getIdPelicula(): \KarfilmsBundle\Entity\Pelicula {
        return $this->idPelicula;
    }

    function getIdActor(): \KarfilmsBundle\Entity\Actor {
        return $this->idActor;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setIdPelicula(\KarfilmsBundle\Entity\Pelicula $idPelicula) {
        $this->idPelicula = $idPelicula;
    }

    function setIdActor(\KarfilmsBundle\Entity\Actor $idActor) {
        $this->idActor = $idActor;
    }


}

