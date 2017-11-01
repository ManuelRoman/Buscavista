<?php

namespace BuscadorBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class EstadisticasController extends Controller
{

    public function mostrarAction(){
        $em = $this->getDoctrine()->getManager();
        $etiqueta_repositorio=$em->getRepository("BuscadorBundle:Etiqueta");
        $etiquetas = $etiqueta_repositorio->findBy(array(), array('estadistica' => 'DESC'), 10);
               
        return $this->render("BuscadorBundle:Estadisticas:mostrar.html.twig", array(
            "etiquetas" =>$etiquetas
        ));
    }
    
}