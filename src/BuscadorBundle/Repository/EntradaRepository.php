<?php
namespace BuscadorBundle\Repository;

use Doctrine\ORM\EntityRepository;
use BuscadorBundle\Entity\EntradaEtiqueta;
use BuscadorBundle\Entity\Etiqueta;

/**
 * Clase repositorio de las Entradas
 */
class EntradaRepository extends EntityRepository
{
    /**
     * Función que realiza las búsquedas, recibe un array de etiquetas
     * @param unknown $etiquetas
     * @return object[]|NULL[]
     */
    public function buscarEntradas($etiquetas){
        $em=$this->getEntityManager();
        $etiqueta_repository=$em->getRepository("BuscadorBundle:Etiqueta");
        $entradaEtiqueta_repository=$em->getRepository("BuscadorBundle:EntradaEtiqueta");
        $entrada_repository=$em->getRepository("BuscadorBundle:Entrada");
        
        //De las etiquetas que se quieren buscar, localizamos las que existen en la bbdd
        $arrayEtiquetas = [];
        for($i=0; $i<count($etiquetas); $i++) {
            $etiquetaEntity = $etiqueta_repository->findByNombre($etiquetas[$i]);
            if ($etiquetaEntity != null){
                $arrayEtiquetas[] = $etiquetaEntity;
            }
         }
         
         //De las etiquetas que exsiten la bbdd y son parte de la búsqueda, obtenemos las entidades que las relacionan con las entradas
         $arrayEntradasEtiquetas = [];
         for($i=0; $i<count($arrayEtiquetas); $i++) {
             $entradaEtiquetaEntity = $entradaEtiqueta_repository->findByEtiqueta($arrayEtiquetas[$i]);
             if ($entradaEtiquetaEntity != null){
                 $arrayEntradasEtiquetas[] = $entradaEtiquetaEntity;
             }
         }
         //Obtenemos los id de las entradas desde la entidad que las relaciona con las etiquetas, en esté array habrá entradas repetidas
         //según el número de etiquetas que coincidan con búsqueda
         $idEntradas = [];
         for($i=0; $i<count($arrayEntradasEtiquetas); $i++) {
             foreach ($arrayEntradasEtiquetas[$i] as $etiquetaEntrada) {
                 $idEntradas[] = $etiquetaEntrada->getEntrada()->getId();
             }
         }
         
         //Unimos las entradas repetidas
         $idEntradasOrdenado = array_count_values($idEntradas);
         //Ordenamos el array de entradas
         arsort($idEntradasOrdenado);
         $entradas = [];
         //Buscamos las entradas en la bbdd
         for($i=0; $i<count($idEntradasOrdenado); $i++) {
             $entradas[] = $entrada_repository->find(key($idEntradasOrdenado));
             next($idEntradasOrdenado);
         }
         //Volvemos a ordenar las entradas según nuestro orden de preferencia
         usort($entradas, function($a, $b) {
             return $a->getPreferencia() < $b->getPreferencia();
         });
         return $entradas;
    }
    
    /**
     * Función que añade la relación entre la entrada y las etiquetas
     * @param unknown $entrada
     * @param unknown $etiquetas
     */
    public function addEtiquetasEntrada($entrada, $etiquetas){
        $em=$this->getEntityManager();
        $etiqueta_repository=$em->getRepository("BuscadorBundle:Etiqueta");
        
        //Añadimos las etiquetas nuevas a la bbdd
        for($i=0; $i<count($etiquetas); $i++) {
            $etiquetaEntity = $etiqueta_repository->findOneBy(array("nombre"=>$etiquetas[$i]));
            if ($etiquetaEntity == null){
                $etiqueta = new Etiqueta();
                $etiqueta->setNombre($etiquetas[$i]);
                $etiqueta->setEstadistica(0);
                $em->persist($etiqueta);
            }
        }
        $em->flush();
        
        // Añadimos la relación entre la entrada y las etiquetas
        for($i=0; $i<count($etiquetas); $i++) {
            $etiquetaEntity = $etiqueta_repository->findOneBy(array("nombre"=>$etiquetas[$i]));
            $entradaEtiqueta =  new EntradaEtiqueta();
            $entradaEtiqueta->setEntrada($entrada);
            $entradaEtiqueta->setEtiqueta($etiquetaEntity);
            $em->persist($entradaEtiqueta);
        }
        $flush = $em->flush();
        
        return $flush;
    }
    
}