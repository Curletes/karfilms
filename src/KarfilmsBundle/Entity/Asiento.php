<?php

namespace KarfilmsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Asiento
 *
 * @ORM\Table(name="asientos", indexes={@ORM\Index(name="fk_sala_idx", columns={"id_sala"})})
 * @ORM\Entity
 */
class Asiento
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
     * @var integer
     *
     * @ORM\Column(name="fila", type="integer", nullable=false)
     */
    private $fila;

    /**
     * @var integer
     *
     * @ORM\Column(name="butaca", type="integer", nullable=false)
     */
    private $butaca;

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

    function getFila() {
        return $this->fila;
    }

    function getButaca() {
        return $this->butaca;
    }

    function getIdSala(): \KarfilmsBundle\Entity\Sala {
        return $this->idSala;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setFila($fila) {
        $this->fila = $fila;
    }

    function setButaca($butaca) {
        $this->butaca = $butaca;
    }

    function setIdSala(\KarfilmsBundle\Entity\Sala $idSala) {
        $this->idSala = $idSala;
    }
}

