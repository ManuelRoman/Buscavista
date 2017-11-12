<?php

namespace BuscadorBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Clase encargada de recibir las peticiones relacionadas con las búsquedas de los usuarios
 */
class BuscarController extends Controller
{
    //Lo declaramos y creamos la sesión a nivel de clase y la tenemos disponible para todos los métodos
    private $session;
    public function __construct(){
        $this->session = new Session();
    }
    
    /**
     * Función que crea el formulario de búsqueda y lo envía a la vista
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
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
     * Función de búsqueda sin paginación, no usado
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
    
    /**
     * Función que recibe el formulario con la petición de búsqueda y envía los
     * resultados a las vista correspondiente
     * @param Request $request
     * @return unknown
     */
    public function resultadosAction(Request $request){
        $datos = $request->get('form');
        try {
            $em = $this->getDoctrine()->getManager();
            $entrada_repository = $em->getRepository("BuscadorBundle:Entrada");
            $etiqueta_repository = $em->getRepository("BuscadorBundle:Etiqueta");
            //Convertimos el string recibido en un array
            $etiquetas = explode(" ", strtolower($datos["terminos"]));
            $etiquetasValidas = [];
            //Eliminamos los elementos ed menos de dos carácteres
            for($i=0; $i<count($etiquetas); $i++) {
                if (strlen($etiquetas[$i]) >2){
                    $etiquetasValidas[] = $etiquetas[$i];
                }
            }
            //LLamamos al repositorio para buscar las entradas de esas etiquetas
            $entradas = $entrada_repository->buscarEntradas($etiquetasValidas);
            //Actualizamos las veces que se han utilizado las etiquetas
            $etiqueta_repository->sumaBusqueda($etiquetasValidas);
        }
        catch(\Doctrine\DBAL\DBALException $e) {
            return $this->render('error.html.twig');
        }
        finally {
            $em->close();
        }
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
    
    /**
     * Función que muestra los detalles de la entrada seleccionada por el usuario
     * recibe como parámetro el id de la entrada
     * @param unknown $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public  function mostrarEntradaAction($id){
        try {
            $em = $this->getDoctrine()->getManager();
            $entrada_repository = $em->getRepository("BuscadorBundle:Entrada");
            $entrada = $entrada_repository->find($id);
        }
        catch(\Doctrine\DBAL\DBALException $e) {
            return $this->render('error.html.twig');
        }
        finally {
            $em->close();
        }
        return $this->render('BuscadorBundle:Buscar:entrada.html.twig', array(
            'entrada' => $entrada,
        ));
    }
    
    /**
     * Función que muestra un pdf de la entrada, recibe el id de la entrada
     * @param unknown $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function entradaPdfAction($id){
        try {
            $em = $this->getDoctrine()->getManager();
            $entrada_repository = $em->getRepository("BuscadorBundle:Entrada");
            $entrada = $entrada_repository->find($id);
        }
        catch(\Doctrine\DBAL\DBALException $e) {
            return $this->render('error.html.twig');
        }
        finally {
            $em->close();
        }
        //Generamos el pdf, se ha tenido que usar FPDF y no KnpSnappyBundle
        //ya que el servidor gratuito no permite su uso
        $pdf = new \FPDF();
        $projectRoot = $this->get('kernel')->getProjectDir();
        $pdf->AddPage();
        $pdf->SetFont('Arial','B',16);
        $urlImagen = $projectRoot."\\web\\imagenes\\".$entrada->getImagen1();
        $pdf->Cell(10,10,utf8_decode($entrada->getTitulo()));
        $pdf->Cell($pdf->Image($urlImagen,75,30,60));
        $pdf->Ln(80);
        $pdf->SetFont('Arial','',12);
        $pdf->MultiCell(0,5,utf8_decode($entrada->getContenido()));       
        return new Response($pdf->Output(), 200, array(
            'Content-Type' => 'application/pdf'));
    }
    
    /**
     * Función que realiza el envío de correos electrónicos, recibe el formulario de la petición
     * @return \Symfony\Component\HttpFoundation\Response|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function enviarEntradaAction(){
        if ($_POST) {
            extract($_POST); //Creamos variables que se llaman igual que los names del formulario
            try{
                $em = $this->getDoctrine()->getManager();
                $entrada_repository = $em->getRepository("BuscadorBundle:Entrada");
                $entrada = $entrada_repository->find($entradaId);
            }
            catch(\Doctrine\DBAL\DBALException $e) {
                return $this->render('error.html.twig');
            }
            finally {
                $em->close();
            }
            $enviado_por='0@gmail.com';
            $enviado_para=$correo;
            $message = \Swift_Message::newInstance()
            ->setSubject('Información solicitada')
            ->setFrom($enviado_por)
            ->setTo($enviado_para)
            ->setBody(
                $this->renderView(
                    'BuscadorBundle:Buscar:enviarEntrada.html.twig', array(
                        'entradaId' => $entradaId,
                        'titulo' =>   $entrada->getTitulo()
                    )),
                'text/html'
                );
            $this->get('mailer')->send($message);
            return $this->render('BuscadorBundle:Buscar:entrada.html.twig', array(
                $this->session->getFlashBag()->add("status", "Correo enviado"),
                'entrada' => $entrada,
            ));
        } else {
            return  $this->redirectToRoute('buscador_buscar');
        }
    }    
}
