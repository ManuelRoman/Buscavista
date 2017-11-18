<?php

namespace BuscadorBundle\Entity;

/**
 * Clase que se usa para persistir en la base de datos la EntradaEtiqueta
 */
class EntradaEtiqueta
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var \BuscadorBundle\Entity\Entrada
     */
    private $entrada;

    /**
     * @var \BuscadorBundle\Entity\Etiqueta
     */
    private $etiqueta;


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
     * Set entrada
     *
     * @param \BuscadorBundle\Entity\Entrada $entrada
     *
     * @return EntradaEtiqueta
     */
    public function setEntrada(\BuscadorBundle\Entity\Entrada $entrada = null)
    {
        $this->entrada = $entrada;

        return $this;
    }

    /**
     * Get entrada
     *
     * @return \BuscadorBundle\Entity\Entrada
     */
    public function getEntrada()
    {
        return $this->entrada;
    }

    /**
     * Set etiqueta
     *
     * @param \BuscadorBundle\Entity\Etiqueta $etiqueta
     *
     * @return EntradaEtiqueta
     */
    public function setEtiqueta(\BuscadorBundle\Entity\Etiqueta $etiqueta = null)
    {
        $this->etiqueta = $etiqueta;

        return $this;
    }

    /**
     * Get etiqueta
     *
     * @return \BuscadorBundle\Entity\Etiqueta
     */
    public function getEtiqueta()
    {
        return $this->etiqueta;
    }
}

