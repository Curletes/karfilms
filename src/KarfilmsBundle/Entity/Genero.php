<?php

namespace KarfilmsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Genero
 *
 * @ORM\Table(name="generos")
 * @ORM\Entity
 */
class Genero
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
     * @var string
     *
     * @ORM\Column(name="genero", type="text", length=65535, nullable=false)
     */
    private $genero;
    function getId() {
        return $this->id;
    }

    function getGenero() {
        return $this->genero;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setGenero($genero) {
        $this->genero = $genero;
    }



}

