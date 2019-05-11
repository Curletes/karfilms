<?php

namespace KarfilmsBundle\Entity;

/**
 * Genero
 */
class Genero
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $genero;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set genero
     *
     * @param string $genero
     *
     * @return Genero
     */
    public function setGenero($genero)
    {
        $this->genero = $genero;

        return $this;
    }

    /**
     * Get genero
     *
     * @return string
     */
    public function getGenero()
    {
        return $this->genero;
    }
}

