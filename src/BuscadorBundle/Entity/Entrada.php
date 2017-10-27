<?php

namespace BuscadorBundle\Entity;

/**
 * Entrada
 */
class Entrada
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $titulo;

    /**
     * @var string
     */
    private $contenido;

    /**
     * @var string
     */
    private $imagen1;
    
    /**
     * @var float
     */
    private $latitud;
    
    /**
     * @var float
     */
    private $longitud;

    /**
     * @var integer
     */
    private $preferencia;


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
     * Set titulo
     *
     * @param string $titulo
     *
     * @return Entrada
     */
    public function setTitulo($titulo)
    {
        $this->titulo = $titulo;

        return $this;
    }

    /**
     * Get titulo
     *
     * @return string
     */
    public function getTitulo()
    {
        return $this->titulo;
    }

    /**
     * Set contenido
     *
     * @param string $contenido
     *
     * @return Entrada
     */
    public function setContenido($contenido)
    {
        $this->contenido = $contenido;

        return $this;
    }

    /**
     * Get contenido
     *
     * @return string
     */
    public function getContenido()
    {
        return $this->contenido;
    }

    /**
     * Set imagen1
     *
     * @param string $imagen1
     *
     * @return Entrada
     */
    public function setImagen1($imagen1)
    {
        $this->imagen1 = $imagen1;

        return $this;
    }

    /**
     * Get imagen1
     *
     * @return string
     */
    public function getImagen1()
    {
        return $this->imagen1;
    }
    
    /**
     * Set latitud
     *
     * @param float $latitud
     *
     * @return Entrada
     */
    public function setLatitud($latitud)
    {
        $this->latitud = $latitud;
        
        return $this;
    }
    
    /**
     * Get latitud
     *
     * @return float
     */
    public function getLatitud()
    {
        return $this->latitud;
    }
    
    /**
     * Set longitud
     *
     * @param float $longitud
     *
     * @return Entrada
     */
    public function setLongitud($longitud)
    {
        $this->longitud = $longitud;
        
        return $this;
    }
    
    /**
     * Get longitud
     *
     * @return float
     */
    public function getLongitud()
    {
        return $this->longitud;
    }

    /**
     * Set preferencia
     *
     * @param integer $preferencia
     *
     * @return Entrada
     */
    public function setPreferencia($preferencia)
    {
        $this->preferencia = $preferencia;

        return $this;
    }

    /**
     * Get preferencia
     *
     * @return integer
     */
    public function getPreferencia()
    {
        return $this->preferencia;
    }
}

