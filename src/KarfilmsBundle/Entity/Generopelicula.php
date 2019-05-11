<?php

namespace KarfilmsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Generopelicula
 *
 * @ORM\Table(name="generospeliculas", indexes={@ORM\Index(name="fk_genero_idx", columns={"id_genero"}), @ORM\Index(name="fk_pelicula_idx", columns={"id_pelicula"})})
 * @ORM\Entity
 */
class Generopelicula
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
     * @var \KarfilmsBundle\Entity\Genero
     *
     * @ORM\ManyToOne(targetEntity="KarfilmsBundle\Entity\Genero")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_genero", referencedColumnName="id")
     * })
     */
    private $idGenero;

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

    function getIdGenero(): \KarfilmsBundle\Entity\Genero {
        return $this->idGenero;
    }

    function getIdPelicula(): \KarfilmsBundle\Entity\Pelicula {
        return $this->idPelicula;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setIdGenero(\KarfilmsBundle\Entity\Genero $idGenero) {
        $this->idGenero = $idGenero;
    }

    function setIdPelicula(\KarfilmsBundle\Entity\Pelicula $idPelicula) {
        $this->idPelicula = $idPelicula;
    }



}

