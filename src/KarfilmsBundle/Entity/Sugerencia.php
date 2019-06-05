<?php

namespace KarfilmsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Sugerencia
 *
 * @ORM\Table(name="sugerencias", indexes={@ORM\Index(name="fk_id_usuario_idx", columns={"id_usuario"})})
 * @ORM\Entity
 */
class Sugerencia
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
     * @ORM\Column(name="texto", type="text", length=65535, nullable=false)
     */
    private $texto;

    /**
     * @var \KarfilmsBundle\Entity\Usuario
     *
     * @ORM\ManyToOne(targetEntity="KarfilmsBundle\Entity\Usuario")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_usuario", referencedColumnName="id")
     * })
     */
    private $idUsuario;
    
    protected $Valoracion;
    
    public function __construct() {
        $this->Valoracion = new ArrayCollection();
    }

    function getId() {
        return $this->id;
    }

    function getTexto() {
        return $this->texto;
    }

    function getIdUsuario(): \KarfilmsBundle\Entity\Usuario {
        return $this->idUsuario;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setTexto($texto) {
        $this->texto = $texto;
    }

    function setIdUsuario(\KarfilmsBundle\Entity\Usuario $idUsuario) {
        $this->idUsuario = $idUsuario;
    }

    public function addActorpelicula(\KarfilmsBundle\Entity\Usuario $usuario) {
        $this->Valoracion[] = $usuario;

        return $this;
    }

    public function getValoracion() {
        return $this->Valoracion;
    }
}

