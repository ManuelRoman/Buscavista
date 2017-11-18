<?php

namespace BuscadorBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use BuscadorBundle\Entity\Etiqueta;
use BuscadorBundle\Form\EtiquetaType;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Clase encargada de gestión de etiquetas por parte del adminstrador
 */
class EtiquetaController extends Controller
{
    //Lo declaramos y creamos la sesión a nivel de clase y la tenemos disponible para todos los métodos
    private $session;
    public function __construct(){
        $this->session = new Session();
    }
    
    /**
     * Función que realiza la creación de etiquetas, envía el formulario al ser llmada por primera vez
     * y la segunda vez recoge elformulario con los datos
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function crearEtiquetaAction(Request $request){
        $etiqueta = new Etiqueta();
        $form = $this->createForm(EtiquetaType::class, $etiqueta);
        $form->handleRequest($request);
        if($form->isSubmitted()){
            if($form->isValid()){
                try{
                    $em = $this->getDoctrine()->getManager();
                    //Comprobamos si la etiqueta ya existe
                    $etiqueta_repositorio=$em->getRepository("BuscadorBundle:Etiqueta");
                    $etiqueta = $etiqueta_repositorio->findOneBy(array("nombre"=>$form->get("nombre")->getData()));
                    if(count($etiqueta)==0){
                        $etiqueta = new Etiqueta();
                        $etiqueta->setNombre(mb_strtolower($form->get("nombre")->getData()));
                        $etiqueta->setEstadistica(0);
                        
                        $em->persist($etiqueta);
                        $flush = $em->flush();
                        
                        if($flush==null){
                            $status = "La etiqueta se ha creado correctamente.";
                        }else{
                            $status = "Error al añadir la etiqueta.";
                        }
                    }else{
                        $status = "La etiqueta no se ha creado, porque ya existe.";
                    }
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
                $this->session->getFlashBag()->add("status", $status);
                //Cuando la etiqueta se ha creado correctamente
                return $this->redirectToRoute("inicio");
            }
            
        }
        return $this->render("BuscadorBundle:Etiqueta:crear.html.twig", array(
            "form" =>$form->createView()
        ));
    }
    
    /**
     * Función que lista todas las etiquetas, utiliza KnpPaginatorBundle,
     * en la consulta utilizamos Doctrine Query Language (DQL), no es un requisito obligatorio
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listarEtiquetasAction(Request $request){
        try{
            $em = $this->getDoctrine()->getManager();
            //Realizamos la consulta a bbdd
            $dql   = "SELECT e FROM BuscadorBundle:Etiqueta e";
            $query = $em->createQuery($dql);
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
        //creamos el $paginator que llama el método get de KnpPaginatorBundle
        $paginator  = $this->get('knp_paginator');
        //le pasamos a $paginator los parámetros y los asignamos a $pagination
        $pagination = $paginator->paginate(
            $query, //origen de los datos
            $request->query->get('page', 1), //número de página por la que empieza
            10 // límite de resultados por página
            );
        return $this->render('BuscadorBundle:Etiqueta:editar.html.twig', array(
            'pagination' => $pagination
        ));
    }
    
    /**
     * Función que edita una etiqueta, recibe el id de la etiqueta, deviuelve a la vista
     * el formulario con los datos de la etiqueta
     * @param Request $request
     * @param unknown $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editarEtiquetaAction(Request $request, $id){
        try{
            $em = $this->getDoctrine()->getManager();
            $etiqueta_repository=$em->getRepository("BuscadorBundle:Etiqueta");
            $etiqueta = $etiqueta_repository->find($id);
        }catch(\Doctrine\DBAL\DBALException $e) {
            //Enviamos un mensaje al log
            $this->get('logger')->error($e->getMessage());
            //Se manda el mensaje al administrador
            $this->session->getFlashBag()->add("status", "Hay un problema con la base de datos, intentelo más tarde.");
            //Se redirige al incio al administrador
            return $this->redirectToRoute("inicio");
        }
        //Creamos el formularío y le damos la etiqueta, nos rellena los campos en la vista
        $form = $this->createForm(EtiquetaType::class, $etiqueta);
        
        //Comprobamos los campos, en este caso no hay validation
        $form->handleRequest($request);
        
        //Si se ha recibido el formulario de editar
        if($form->isSubmitted()){
            if($form->isValid()){
                //cambiamos los campos
                $etiqueta->setNombre(strtolower($form->get("nombre")->getData()));
                try{
                    $em->persist($etiqueta);
                    $flush = $em->flush();
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
                if($flush==null){
                    $status = "La etiqueta se ha editado correctamente.";
                }else{
                    $status = "Error al editar la etiqueta.";
                }
            }else{
                $status = "La etiqueta no se ha editado, compruebe el campo.";
            }
            //Enviamos el mensaje de información
            $this->session->getFlashBag()->add("status", $status);
            //Cuando el administrador se ha editado correctamente
            return $this->redirectToRoute("inicio");
        }
        
        return $this->render("BuscadorBundle:Etiqueta:formularioEditar.html.twig", array(
            "form" =>$form->createView()
        ));
    }
}