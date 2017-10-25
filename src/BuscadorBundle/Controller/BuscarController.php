<?php

namespace BuscadorBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class BuscarController extends Controller
{
    public function buscarAction(Request $request)
    {
        $form = $this->createFormBuilder()
        ->add('terminos', TextType::class, array("label"=>"Introduzca las palabras de la búsqueda", "required"=>"required", "attr" => array("class" =>"form-name form-control")))
        ->add('buscar', SubmitType::class, array("attr" => array("class" =>"btn btn-default")))
        ->getForm();
        return $this->render('BuscadorBundle:Buscar:buscar.html.twig', array(
            'form'=>$form->createView(),
        ));
    }
    
    /**
     * Método de búsqueda sin paginación
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function resultados2Action(Request $request){
        $datos = $request->get('form');
        $em = $this->getDoctrine()->getManager();
        $entrada_repository = $em->getRepository("BuscadorBundle:Entrada");
        $etiqueta_repository = $em->getRepository("BuscadorBundle:Etiqueta");
        
        $etiquetas = explode(" ", strtolower($datos["terminos"]));
        $etiquetasValidas = [];
        for($i=0; $i<count($etiquetas); $i++) {
            if (strlen($etiquetas[$i]) >2){
                $etiquetasValidas[] = $etiquetas[$i];
            }
        }
        
        $entradas = $entrada_repository->buscarEntradas($etiquetasValidas);
        $correcto = $etiqueta_repository->sumaBusqueda($etiquetasValidas);
        if($correcto != null){
            //Problema con la bbdd
        }
        
        return $this->render('BuscadorBundle:Buscar:resultados.html.twig', array(
            'entradas' => $entradas,
        ));
    }
    
    public function resultadosAction(Request $request){
        $datos = $request->get('form');
        $em = $this->getDoctrine()->getManager();
        $entrada_repository = $em->getRepository("BuscadorBundle:Entrada");
        $etiqueta_repository = $em->getRepository("BuscadorBundle:Etiqueta");
        
        $etiquetas = explode(" ", strtolower($datos["terminos"]));
        $etiquetasValidas = [];
        for($i=0; $i<count($etiquetas); $i++) {
            if (strlen($etiquetas[$i]) >2){
                $etiquetasValidas[] = $etiquetas[$i];
            }
        }
        
        $entradas = $entrada_repository->buscarEntradas($etiquetasValidas);
        $etiqueta_repository->sumaBusqueda($etiquetasValidas);
        
        //creamos el $paginator que llama el método get de KnpPaginatorBundle
        $paginator  = $this->get('knp_paginator');
        //le pasamos a $paginator los parámetros y los asignamos a $pagination
        $pagination = $paginator->paginate(
            $entradas, //origen de los datos
            $request->query->get('page', 1), //número de página por la que empieza
            5 // límite de resultados por página
            );
        return $this->render('BuscadorBundle:Buscar:resultadosPaginador.html.twig', array(
            'pagination' => $pagination
        ));
    }
    
    public  function mostrarEntradaAction($id){
        $em = $this->getDoctrine()->getManager();
        $entrada_repository = $em->getRepository("BuscadorBundle:Entrada");
        $entrada = $entrada_repository->find($id);
        return $this->render('BuscadorBundle:Buscar:entrada.html.twig', array(
            'entrada' => $entrada,
        ));
    }
}
