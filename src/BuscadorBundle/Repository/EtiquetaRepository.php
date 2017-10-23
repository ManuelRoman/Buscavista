<?php
namespace BuscadorBundle\Repository;

use Doctrine\ORM\EntityRepository;

class EtiquetaRepository extends EntityRepository
{
    public function sumaBusqueda($etiquetas){
        $em=$this->getEntityManager();
        $etiqueta_repository=$em->getRepository("BuscadorBundle:Etiqueta");
        for($i=0; $i<count($etiquetas); $i++) {
            $etiquetaEntity = $etiqueta_repository->findByNombre($etiquetas[$i]);
            if ($etiquetaEntity != null){
                $estadistica = $etiquetaEntity[0]->getEstadistica();
                $estadistica++;
                $etiquetaEntity[0]->setEstadistica($estadistica);
                $em->persist($etiquetaEntity[0]);
            }
        }
        $flush = $em->flush();
        return $flush;
    }
}