<?php

namespace BuscadorBundle\Entity;

/**
 * Etiqueta
 */
class Etiqueta
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $nombre;

    /**
     * @var integer
     */
    private $estadistica;


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
     * Set nombre
     *
     * @param string $nombre
     *
     * @return Etiqueta
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get nombre
     *
     * @return string
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Set estadistica
     *
     * @param integer $estadistica
     *
     * @return Etiqueta
     */
    public function setEstadistica($estadistica)
    {
        $this->estadistica = $estadistica;

        return $this;
    }

    /**
     * Get estadistica
     *
     * @return integer
     */
    public function getEstadistica()
    {
        return $this->estadistica;
    }
}

