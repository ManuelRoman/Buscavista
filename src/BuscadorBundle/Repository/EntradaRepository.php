<?php
namespace BuscadorBundle\Repository;

use Doctrine\ORM\EntityRepository;
use BuscadorBundle\Entity\EntradaEtiqueta;
use BuscadorBundle\Entity\Etiqueta;

class EntradaRepository extends EntityRepository
{
    public function buscarEntradas($etiquetas){
        $em=$this->getEntityManager();
        $etiqueta_repository=$em->getRepository("BuscadorBundle:Etiqueta");
        $entradaEtiqueta_repository=$em->getRepository("BuscadorBundle:EntradaEtiqueta");
        $entrada_repository=$em->getRepository("BuscadorBundle:Entrada");
        
        $arrayEtiquetas = [];
        for($i=0; $i<count($etiquetas); $i++) {
            $etiquetaEntity = $etiqueta_repository->findByNombre($etiquetas[$i]);
            if ($etiquetaEntity != null){
                $arrayEtiquetas[] = $etiquetaEntity;
            }
         }
         
         $arrayEntradasEtiquetas = [];
         for($i=0; $i<count($arrayEtiquetas); $i++) {
             $entradaEtiquetaEntity = $entradaEtiqueta_repository->findByEtiqueta($arrayEtiquetas[$i]);
             if ($entradaEtiquetaEntity != null){
                 $arrayEntradasEtiquetas[] = $entradaEtiquetaEntity;
             }
         }
         $idEntradas = [];
         for($i=0; $i<count($arrayEntradasEtiquetas); $i++) {
             foreach ($arrayEntradasEtiquetas[$i] as $etiquetaEntrada) {
                 $idEntradas[] = $etiquetaEntrada->getEntrada()->getId();
             }
         }
         
         $idEntradasOrdenado = array_count_values($idEntradas);
         arsort($idEntradasOrdenado);
         $entradas = [];
         for($i=0; $i<count($idEntradasOrdenado); $i++) {
             $entradas[] = $entrada_repository->find(key($idEntradasOrdenado));
             next($idEntradasOrdenado);
         }
         usort($entradas, function($a, $b) {
             return $a->getPreferencia() < $b->getPreferencia();
         });
         return $entradas;
    }
    
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