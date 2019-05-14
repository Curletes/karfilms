<?php

namespace KarfilmsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Sala
 *
 * @ORM\Table(name="salas", uniqueConstraints={@ORM\UniqueConstraint(name="nombre_UNIQUE", columns={"nombre"})})
 * @ORM\Entity
 */
class Sala
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
     * @ORM\Column(name="nombre", type="string", length=1, nullable=false)
     */
    private $nombre;
    
    protected $asiento;
    
    public function __construct() {
        $this->asiento = new ArrayCollection();
    }

    function getId() {
        return $this->id;
    }

    function getNombre() {
        return $this->nombre;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setNombre($nombre) {
        $this->nombre = $nombre;
    }

    public function getAsientos() {
        return $this->asiento;
    }
}

