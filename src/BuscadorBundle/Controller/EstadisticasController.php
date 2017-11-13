<?php

namespace BuscadorBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Clase encargada de recoger las estadísticas de uso de las etiquetas
 */
class EstadisticasController extends Controller
{

    /**
     * Función que busca las etiquetas más utilizadas en las búsquedas de los usuarios
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function mostrarAction(){
        try{
            $em = $this->getDoctrine()->getManager();
            $etiqueta_repositorio=$em->getRepository("BuscadorBundle:Etiqueta");
            $etiquetas = $etiqueta_repositorio->findBy(array(), array('estadistica' => 'DESC'), 10);
        }catch(\Doctrine\DBAL\DBALException $e) {
            //Enviamos un mensaje al log
            $this->get('logger')->error($e->getMessage());
            //Se manda el mensaje al administrador
            $this->session->getFlashBag()->add("status", "Hay un problema con la base de datos, intentelo más tarde.");
            //Se redirige al incio al administrador
            return $this->redirectToRoute("inicio");
        } finally {
            //Cerramos el EntityManager
            $em->close();
        }
               
        return $this->render("BuscadorBundle:Estadisticas:mostrar.html.twig", array(
            "etiquetas" =>$etiquetas
        ));
    }
    
}