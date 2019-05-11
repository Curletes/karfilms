<?php

namespace KarfilmsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Directorpelicula
 *
 * @ORM\Table(name="directorespeliculas", indexes={@ORM\Index(name="fk_id_director_idx", columns={"id_director"}), @ORM\Index(name="fk_id_pelicula_idx", columns={"id_pelicula"})})
 * @ORM\Entity
 */
class Directorpelicula
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
     * @var \KarfilmsBundle\Entity\Director
     *
     * @ORM\ManyToOne(targetEntity="KarfilmsBundle\Entity\Director")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_director", referencedColumnName="id")
     * })
     */
    private $idDirector;

    /**
     * @var \KarfilmsBundle\Entity\Pelicula
     *
     * @ORM\ManyToOne(targetEntity="KarfilmsBundle\Entity\Pelicula")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_pelicula", referencedColumnName="id")
     * })
     */
    private $idPelicula;
    function getId() {
        return $this->id;
    }

    function getIdDirector(): \KarfilmsBundle\Entity\Director {
        return $this->idDirector;
    }

    function getIdPelicula(): \KarfilmsBundle\Entity\Pelicula {
        return $this->idPelicula;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setIdDirector(\KarfilmsBundle\Entity\Director $idDirector) {
        $this->idDirector = $idDirector;
    }

    function setIdPelicula(\KarfilmsBundle\Entity\Pelicula $idPelicula) {
        $this->idPelicula = $idPelicula;
    }



}

