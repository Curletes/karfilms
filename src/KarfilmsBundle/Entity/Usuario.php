<?php

namespace KarfilmsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Usuario
 *
 * @ORM\Table(name="usuarios", uniqueConstraints={@ORM\UniqueConstraint(name="nombre_UNIQUE", columns={"nombre"}), @ORM\UniqueConstraint(name="email_UNIQUE", columns={"email"})})
 * @ORM\Entity
 */
class Usuario implements UserInterface
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
     * @ORM\Column(name="nombre", type="string", length=45, nullable=false)
     */
    private $nombre;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=45, nullable=false)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255, nullable=false)
     */
    private $password;

    /**
     * @var string
     *
     * @ORM\Column(name="estado", type="string", length=45, nullable=false)
     */
    private $estado = "Conectado";

    /**
     * @var string
     *
     * @ORM\Column(name="rol", type="string", length=45, nullable=false)
     */
    private $rol = "ROLE_USER";

    /**
     * @var string
     *
     * @ORM\Column(name="icono", type="string", length=45, nullable=false)
     */
    private $icono = "anonimo.png";
    
    // AUTH
    
    public function getUsername() {
        return $this->nombre;
    }
    
    public function getSalt() {
        return null;
    }
    
    public function getRoles() {
        return array($this->getRol());
    }
    
    public function eraseCredentials() {
        
    }
    
    // END AUTH
    
    function getId() {
        return $this->id;
    }

    function getNombre() {
        return $this->nombre;
    }

    function getEmail() {
        return $this->email;
    }

    function getPassword() {
        return $this->password;
    }

    function getEstado() {
        return $this->estado;
    }

    function getRol() {
        return $this->rol;
    }

    function getIcono() {
        return $this->icono;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setNombre($nombre) {
        $this->nombre = $nombre;
    }

    function setEmail($email) {
        $this->email = $email;
    }

    function setPassword($password) {
        $this->password = $password;
    }

    function setEstado($estado) {
        $this->estado = $estado;
    }

    function setRol($rol) {
        $this->rol = $rol;
    }

    function setIcono($icono) {
        $this->icono = $icono;
    }
}

