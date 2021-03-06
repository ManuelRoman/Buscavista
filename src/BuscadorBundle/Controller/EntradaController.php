<?php

namespace BuscadorBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use BuscadorBundle\Entity\Entrada;
use BuscadorBundle\Form\EntradaType;

/**
 * Clase encargada de la gestión administrativa de las entradas
 * sólo es usada por los administradores
 */
class EntradaController extends Controller
{
    //Lo declaramos y creamos la sesión a nivel de clase y la tenemos disponible para todos los métodos
    private $session;
    public function __construct(){
        $this->session = new Session();
    }
    
    /**
     * Función que se encarga de crear una entrada, envía el formulario vacío a la vista
     * y si recibe uno crea la entrada con los datos contenidos en él
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function crearEntradaAction(Request $request){
        $entrada = new Entrada();
        $form = $this->createForm(EntradaType::class, $entrada);
        $form->handleRequest($request);
        //Si no es la primera vez que se llama ha esta función sólo crea el formulario y lo envía a la vista
        if($form->isSubmitted()){
            //Si el fomulario es recibido y es válido crea la entrada
            if($form->isValid()){
                try {
                    $em = $this->getDoctrine()->getManager();
                    $entrada_repository = $em->getRepository("BuscadorBundle:Entrada");
                    
                    $entrada = new Entrada();
                    $entrada->setTitulo($form->get("titulo")->getData());
                    $entrada->setContenido($form->get("contenido")->getData());
                    $entrada->setLatitud($form->get("latitud")->getData());
                    $entrada->setLongitud($form->get("longitud")->getData());
                    $entrada->setPreferencia($form->get("preferencia")->getData());
                    
                    //Capturamos el archivo subido obtenemos la extensión le ponemos de nombre la hora y fecha para que no coincida
                    $imagen = $form["imagen1"]->getData();
                    $ext = $imagen->guessExtension();
                    $nombreImagen = time().".".$ext;
                    $imagen->move("imagenes", $nombreImagen);
                    $entrada->setImagen1($nombreImagen);
                    
                    //Guardamos la entrada en bb.dd.
                    $em->persist($entrada);
                    $flush = $em->flush();
                    
                    //Guardamos las etiquetas de la entrada
                    $etiquetas = explode(" ", mb_strtolower($form->get("etiquetas")->getData()));
                    $etiquetasValidas = [];
                    for($i=0; $i<count($etiquetas); $i++) {
                        if (strlen($etiquetas[$i]) >2){
                            $etiquetasValidas[] = $etiquetas[$i];
                        }
                    }
                    $flush = $entrada_repository->addEtiquetasEntrada($entrada, $etiquetasValidas);
                }
                catch(\Doctrine\DBAL\DBALException $e) {
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
                    $status = "La entrada se ha creado correctamente.";
                }else{
                    $status = "Error al añadir la entrada.";
                }
                //Se manda el mensaje al administrador
                $this->session->getFlashBag()->add("status", $status);
                //Al finalizar el proceso de cración de la entrada
                return $this->redirectToRoute("inicio");
            }
        }
        return $this->render("BuscadorBundle:Entrada:crear.html.twig", array(
            "form" =>$form->createView()
        ));
    }
    
    /**
     * Función que muestra un listado con todas las entradas a los administradores
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listarTodasEntradasAction(Request $request){
        try{
            $em = $this->getDoctrine()->getManager();
            //Realizamos la consulta a bbdd
            $dql   = "SELECT e FROM BuscadorBundle:Entrada e";
            $query = $em->createQuery($dql);
            
            //creamos el $paginator que llama el método get de KnpPaginatorBundle
            $paginator  = $this->get('knp_paginator');
            //le pasamos a $paginator los parámetros y los asignamos a $pagination
            $pagination = $paginator->paginate(
                $query, //origen de los datos
                $request->query->get('page', 1), //número de página por la que empieza
                5 // límite de resultados por página
                );
        } catch(\Doctrine\DBAL\DBALException $e) {
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
        return $this->render('BuscadorBundle:Entrada:editar.html.twig', array(
            'pagination' => $pagination
        ));
    }
    
    /**
     * Función que realiza el editado de una entrada, recibe el id de la entrada
     * @param Request $request
     * @param unknown $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editarEntradaAction(Request $request, $id){
        try{
            $em = $this->getDoctrine()->getManager();
            $entrada_repository=$em->getRepository("BuscadorBundle:Entrada");
            $entrada = $entrada_repository->find($id);
        } catch(\Doctrine\DBAL\DBALException $e) {
            //Cerramos el EntityManager
            $em->close();
            //Enviamos un mensaje al log
            $this->get('logger')->error($e->getMessage());
            //Se manda el mensaje al administrador
            $this->session->getFlashBag()->add("status", "Hay un problema con la base de datos, intentelo más tarde.");
            //Se redirige al incio al administrador
            return $this->redirectToRoute("inicio");
        }
        //Obtenemos la imagen de la bbdd por si no se cambia
        $imagenEntrada = $entrada->getImagen1();
        //Recorremos las etiquetas para pasarselas a la vista
        $etiquetas="";
        foreach ($entrada->getEntradaEtiqueta() as $entradaEtiqueta){
            $etiquetas.=$entradaEtiqueta->getEtiqueta()->getNombre()." ";
        }
              
        //Creamos el formularío y le damos la entrada, nos rellena los campos en la vista
        $form = $this->createForm(EntradaType::class, $entrada);
        
        //Comprobamos los campos, en este caso no hay validation
        $form->handleRequest($request);
        
        //Si se ha recibido el formulario de editar el administrador
        if($form->isSubmitted()){
            if($form->isValid()){
                //cambiamos los campos
                $entrada->setTitulo($form->get("titulo")->getData());
                $entrada->setContenido($form->get("contenido")->getData());
                $entrada->setLatitud($form->get("latitud")->getData());
                $entrada->setLongitud($form->get("longitud")->getData());
                $entrada->setPreferencia($form->get("preferencia")->getData());
                
                //Capturamos el archivo subido obtenemos la extensión le ponemos de nombre la hora y fecha para que no coincida
                $imagen = $form["imagen1"]->getData();
                if(!empty($imagen) && $imagen!=null){
                    $ext = $imagen->guessExtension();
                    $nombreImagen = time().".".$ext;
                    $imagen->move("imagenes", $nombreImagen);
                    $entrada->setImagen1($nombreImagen);
                } else {
                    $entrada->setImagen1($imagenEntrada);
                }
                
                try{
                    $em->persist($entrada);
                    $flush = $em->flush();
                    
                    //Eliminamos todas las entradasEtiquetas
                    $entradaEtiqueta_repository = $em->getRepository("BuscadorBundle:EntradaEtiqueta");
                    $entradaEtiquetas = $entradaEtiqueta_repository->findBy(array("entrada"=>$entrada));
                    foreach ($entradaEtiquetas as $et){
                        //hay problemas con algunas tags que estan a null, de entradas anterioes que no tienen tags
                        //Eliminar en la versión definitiva
                        if(is_object($et)){
                            $em->remove($et);
                            $em->flush();
                        }
                    }
                    
                    //Guardamos las etiquetas de la entrada
                    $etiquetas = explode(" ", mb_strtolower($form->get("etiquetas")->getData()));
                    $etiquetasValidas = [];
                    for($i=0; $i<count($etiquetas); $i++) {
                        if (strlen($etiquetas[$i]) >2){
                            $etiquetasValidas[] = $etiquetas[$i];
                        }
                    }
                    $flush = $entrada_repository->addEtiquetasEntrada($entrada, $etiquetasValidas);
                } catch(\Doctrine\DBAL\DBALException $e) {
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
                    $status = "La entrada se ha editado correctamente.";
                }else{
                    $status = "Error al editar la entrada.";
                }
            }else{
                $status = "La entrada no se ha editado, compruebe el campo.";
            }
            //Enviamos el mensaje de información
            $this->session->getFlashBag()->add("status", $status);
            //Cuando la entrada se ha editado correctamente
            return $this->redirectToRoute("inicio");
        }
        
        
        return $this->render("BuscadorBundle:Entrada:formularioEditar.html.twig", array(
            "form" =>$form->createView(),
            "imagenEntrada" => $imagenEntrada,
            "etiquetas" => trim($etiquetas)
        ));
    }
    
    /**
     * Dunción que elimina una entrada, recibe el id de la entrada
     * @param unknown $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function eliminarEntradaAction($id){
        try{
            $em = $this->getDoctrine()->getManager();
            $entrada_repository=$em->getRepository("BuscadorBundle:Entrada");
            $entradaEtiqueta_repository = $em->getRepository("BuscadorBundle:EntradaEtiqueta");
            $entrada = $entrada_repository->find($id);
            //Borramos la imagen
            $imagen=$entrada->getImagen1();
            unlink("imagenes/".$imagen);
            //Hay que eliminar las entradasEtiquetas asociadas a las entradas, ya que hay una clave ajena
            $entradaEtiquetas = $entradaEtiqueta_repository->findBy(array("entrada"=>$entrada));
            foreach ($entradaEtiquetas as $et){
                if(is_object($et)){
                    $em->remove($et);
                    $em->flush();
                }
            }
            if(is_object($entrada)){
               $em->remove($entrada);
                $flush = $em->flush();
            }
        } catch(\Doctrine\DBAL\DBALException $e) {
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
            $status = "La entrada se ha eliminado correctamente.";
        }else{
            $status = "Error al eliminar la entrada.";
        }
        //Enviamos el mensaje de información
        $this->session->getFlashBag()->add("status", $status);
        //Cuando la entrada se ha eliminado correctamente
        return $this->redirectToRoute("inicio");
    }
    
    /**
     * Dunción que busca una entrada por su título
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function buscarEntradaAction(Request $request){
        if ($_GET) {
            extract($_GET); //Creamos variables que se llaman igual que los names del formulario
            try{
                $em = $this->getDoctrine()->getManager();
                $entrada_repository=$em->getRepository("BuscadorBundle:Entrada");
                $entrada = $entrada_repository->findOneBy(array("titulo"=>$titulo));
                if ($entrada != null){
                    return $this->editarEntradaAction($request, $entrada->getId());
                } else {
                    $this->session->getFlashBag()->add("status", "No se ha encontrado una entrada con ese título.");
                    return $this->redirectToRoute("inicio");
                }
            } catch(\Doctrine\DBAL\DBALException $e) {
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
        } else {
            return $this->redirectToRoute("inicio");
        }
    }
    
}