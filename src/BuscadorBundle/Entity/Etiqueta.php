<?php

namespace BuscadorBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Clase que se usa para persistir en la base de datos la Etiqueta
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
     * Usada en la relación oneToMany para
     * la creación de entradas
     * @var unknown
     */
    protected $entradaEtiqueta;
    
    /**
     * Usada en la relación oneToMany para
     * la creación de entradas
     */
    public function  __construct(){
        $this->entradaEtiqueta = new ArrayCollection();
    }


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
    
    /**
     * @return the $entradaEtiqueta
     */
    public function getEntradaEtiqueta()
    {
        return $this->entradaEtiqueta;
    }

    
    
    
}

