<?php

namespace KarfilmsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Valoracion
 *
 * @ORM\Table(name="valoraciones", uniqueConstraints={@ORM\UniqueConstraint(name="fk_valoracion_idx", columns={"id_usuario", "id_sugerencia"})} indexes={@ORM\Index(name="fk_id_user_idx", columns={"id_usuario"}), @ORM\Index(name="fk_id_sugerencia_idx", columns={"id_sugerencia"})})
 * @ORM\Entity
 */
class Valoracion
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
     * @var \KarfilmsBundle\Entity\Sugerencia
     *
     * @ORM\ManyToOne(targetEntity="KarfilmsBundle\Entity\Sugerencia")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_sugerencia", referencedColumnName="id")
     * })
     */
    private $idSugerencia;

    /**
     * @var \KarfilmsBundle\Entity\Usuario
     *
     * @ORM\ManyToOne(targetEntity="KarfilmsBundle\Entity\Usuario")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_usuario", referencedColumnName="id")
     * })
     */
    private $idUsuario;

    function getId() {
        return $this->id;
    }

    function getIdSugerencia(): \KarfilmsBundle\Entity\Sugerencia {
        return $this->idSugerencia;
    }

    function getIdUsuario(): \KarfilmsBundle\Entity\Usuario {
        return $this->idUsuario;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setIdSugerencia(\KarfilmsBundle\Entity\Sugerencia $idSugerencia) {
        $this->idSugerencia = $idSugerencia;
    }

    function setIdUsuario(\KarfilmsBundle\Entity\Usuario $idUsuario) {
        $this->idUsuario = $idUsuario;
    }


}

