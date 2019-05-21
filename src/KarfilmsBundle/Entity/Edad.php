<?php

namespace KarfilmsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Edad
 *
 * @ORM\Table(name="edades")
 * @ORM\Entity
 */
class Edad
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
     * @ORM\Column(name="clasificacion", type="string", length=255, nullable=false)
     */
    private $clasificacion;
    
    protected $pelicula;
    
    public function __construct() {
        $this->pelicula = new ArrayCollection();
    }
    
    public function __toString() {
        return $this->getClasificacion();
    }

    function getId() {
        return $this->id;
    }

    function getClasificacion() {
        return $this->clasificacion;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setClasificacion($clasificacion) {
        $this->clasificacion = $clasificacion;
    }

    public function getPeliculas() {
        return $this->pelicula;
    }
}

