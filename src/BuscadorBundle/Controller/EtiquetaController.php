<?php

namespace BuscadorBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use BuscadorBundle\Entity\Etiqueta;
use BuscadorBundle\Form\EtiquetaType;
use Symfony\Component\HttpFoundation\Session\Session;

class EtiquetaController extends Controller
{
    private $session;
    //Lo declaramos y creamos a nivel de clase y lo tenemos para todos los métodos
    public function __construct(){
        $this->session = new Session();
    }
    
    public function crearEtiquetaAction(Request $request){
        $etiqueta = new Etiqueta();
        $form = $this->createForm(EtiquetaType::class, $etiqueta);
        $form->handleRequest($request);
        if($form->isSubmitted()){
            if($form->isValid()){
                $em = $this->getDoctrine()->getManager();
                //Comprobamos si la etiqueta ya existe
                $etiqueta_repositorio=$em->getRepository("BuscadorBundle:Etiqueta");
                $etiqueta = $etiqueta_repositorio->findOneBy(array("nombre"=>$form->get("nombre")->getData()));
                if(count($etiqueta)==0){
                    $etiqueta = new Etiqueta();
                    $etiqueta->setNombre(strtolower($form->get("nombre")->getData()));
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
                $this->session->getFlashBag()->add("status", $status);
                //Cuando la etiqueta se ha creado correctamente
                return $this->redirectToRoute("inicio");
            }
        }
        return $this->render("BuscadorBundle:Etiqueta:crear.html.twig", array(
            "form" =>$form->createView()
        ));
    }
    
    public function listarEtiquetasAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        //Realizamos la consulta a bbdd
        $dql   = "SELECT e FROM BuscadorBundle:Etiqueta e";
        $query = $em->createQuery($dql);
        
        //creamos el $paginator que llama el método get de KnpPaginatorBundle
        $paginator  = $this->get('knp_paginator');
        //le pasamos a $paginator los parámetros y los asignamos a $pagination
        $pagination = $paginator->paginate(
            $query, //origen de los datos
            $request->query->get('page', 1), //número de página por la que empieza
            3 // límite de resultados por página
            );
        return $this->render('BuscadorBundle:Etiqueta:editar.html.twig', array(
            'pagination' => $pagination
        ));
    }
    
    public function editarEtiquetaAction(Request $request, $id){
        $em = $this->getDoctrine()->getManager();
        $etiqueta_repository=$em->getRepository("BuscadorBundle:Etiqueta");
        $etiqueta = $etiqueta_repository->find($id);
        
        //Creamos el formularío y le damos el usuario, nos rellena los campos en la vista
        $form = $this->createForm(EtiquetaType::class, $etiqueta);
        
        //Comprobamos los campos, en este caso no hay validation
        $form->handleRequest($request);
        
        //Si se ha recibido el formulario de editar el administrador
        if($form->isSubmitted()){
            if($form->isValid()){
                //cambiamos los campos del administrador
                $etiqueta->setNombre(strtolower($form->get("nombre")->getData()));
                $em->persist($etiqueta);
                $flush = $em->flush();
                
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