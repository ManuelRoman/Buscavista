<?php

namespace BuscadorBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use BuscadorBundle\Entity\Usuario;
use BuscadorBundle\Form\UsuarioType;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Clase encargada de la gestión de usuarios, en el contexto de la aplicación sería
 * de los usuarios administradores
 */
class UsuarioController extends Controller
{
    //Lo declaramos y creamos la sesión a nivel de clase y la tenemos disponible para todos los métodos
    private $session;
    public function __construct(){
        $this->session = new Session();
    }
    
    /**
     * Función que realiza el logueo de usuarios
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function loginAction(Request $request)
    {
        //Autetificación del usuario
        $authenticationUtils = $this->get("security.authentication_utils");
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();
        if ($error != null){
            $this->session->getFlashBag()->add("status", "Compruebe el usuario y contraseña");
        }
        
        //Mandamos a la vista el usuario y/o error
        return $this->render('BuscadorBundle:Usuario:login.html.twig', array(
            "last_username" => $lastUsername
        ));
    }
    
    /**
     * Función que redirige al incio
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function inicioAction(Request $request)
    {
        return $this->render('BuscadorBundle:Usuario:inicio.html.twig');
    }
    
    /**
     * Función que realiza el registro de usuarios con el rol administrador, envía el formulario vacío, cuando es llamada
     * por primera vez y lo recoge cuando es llmada por segunda vez
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function  creaAdminAction(Request $request){
        $usuario = new Usuario();
        $form = $this->createForm(UsuarioType::class, $usuario);
        $form->handleRequest($request);
        if($form->isSubmitted()){
            if($form->isValid()){
                try{
                    $em = $this->getDoctrine()->getManager();
                    //Comprobamos si el email ya esta registrado
                    $usuario_repository=$em->getRepository("BuscadorBundle:Usuario");
                    $usuario = $usuario_repository->findOneBy(array("email"=>$form->get("email")->getData()));
                    if(count($usuario)==0){
                        $usuario = new Usuario();
                        $usuario->setNombre($form->get("nombre")->getData());
                        $usuario->setApellido($form->get("apellido")->getData());
                        $usuario->setEmail($form->get("email")->getData());
                        $usuario->setRole("ROLE_ADMIN");
                        //Ciframos la contraseña
                        $factory = $this->get("security.encoder_factory");
                        $encoder = $factory->getEncoder($usuario);
                        $password = $encoder->encodePassword($form->get("password")->getData(), $usuario->getSalt());
                        $usuario->setPassword($password);
                        
                        $em->persist($usuario);
                        $flush = $em->flush();
                        
                        if($flush==null){
                            $status = "El usuario se ha creado correctamente.";
                        }else{
                            $status = "Error al crear el usuario.";
                        }
                    } else {
                        $status = "El Email ya esta registrado";
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
                return $this->redirectToRoute("inicio");
            }
        }
        
        return $this->render("BuscadorBundle:Usuario:crear.html.twig", array(
            "form" =>$form->createView()
        ));
        
    }
    
    /**
     * Método sin paginador, no se usa
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listarAdmins2Action(){
        $em = $this->getDoctrine()->getManager();
        $usuario_repository=$em->getRepository("BuscadorBundle:Usuario");
        $administradores = $usuario_repository->findBy(array('role' => 'ROLE_ADMIN'));
        return $this->render("BuscadorBundle:Usuario:eliminarEditar.html.twig", array(
            "administradores" =>$administradores
        ));
    }
    
    /**
     * Función que muestra una lista de los usuarios con rol administrador, utiliza KnpPaginatorBundle
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listarAdminsAction(Request $request){
        try{
            $em = $this->getDoctrine()->getManager();
            //Realizamos la consulta a bbdd
            $dql   = "SELECT u FROM BuscadorBundle:Usuario u";
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
            5 // límite de resultados por página
            );
        return $this->render('BuscadorBundle:Usuario:eliminarEditarPaginador.html.twig', array(
            'pagination' => $pagination
        ));
    }
    
    /**
     * Función que realiza el proceso de eliminar un usuario con rol administrador, recibe el id del administrador a eliminar
     * @param Request $request
     * @param unknown $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function eliminarAdminAction(Request $request, $id){
        try{
            $em = $this->getDoctrine()->getManager();
            $usuario_repository=$em->getRepository("BuscadorBundle:Usuario");
            $usuario = $usuario_repository->find($id);
            $em->remove($usuario);
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
            $status = "El administrador se ha eliminado correctamente.";
        }else{
            $status = "Error al eliminar el administrador.";
        }
        $this->session->getFlashBag()->add("status", $status);
        return $this->render("BuscadorBundle:Usuario:inicio.html.twig");
    }
    
    /**
     * Función que edita un usuario con rol de administrador, recibe el id del administrador
     * lo recupera de la bbdd y lo envía junto al formulario
     * @param Request $request
     * @param unknown $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editarAdminAction(Request $request, $id){
        try{
            $em = $this->getDoctrine()->getManager();
            $usuario_repository=$em->getRepository("BuscadorBundle:Usuario");
            $usuario = $usuario_repository->find($id);
        }catch(\Doctrine\DBAL\DBALException $e) {
            //Enviamos un mensaje al log
            $this->get('logger')->error($e->getMessage());
            //Se manda el mensaje al administrador
            $this->session->getFlashBag()->add("status", "Hay un problema con la base de datos, intentelo más tarde.");
            //Se redirige al incio al administrador
            return $this->redirectToRoute("inicio");
        }
        
        //Creamos el formulario y le damos el usuario, nos rellena los campos en la vista
        $form = $this->createForm(UsuarioType::class, $usuario);
        
        //Comprobamos los campos, en este caso no hay validation
        $form->handleRequest($request);
        
        //Si se ha recibido el formulario de editar el administrador
        if($form->isSubmitted()){
            if($form->isValid()){
                //cambiamos los campos del administrador
                $usuario->setNombre($form->get("nombre")->getData());
                $usuario->setApellido($form->get("apellido")->getData());
                $usuario->setEmail($form->get("email")->getData());
                //Ciframos la contraseña
                $factory = $this->get("security.encoder_factory");
                $encoder = $factory->getEncoder($usuario);
                $password = $encoder->encodePassword($form->get("password")->getData(), $usuario->getSalt());
                $usuario->setPassword($password);
                try{
                    $em->persist($usuario);
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
                    $status = "El administrador se ha editado correctamente.";
                }else{
                    $status = "Error al editar el administrador.";
                }
            }else{
                $status = "El administrador no se ha editado, compruebe los campos.";
            }
            //Enviamos el mensaje de información
            $this->session->getFlashBag()->add("status", $status);
            //Cuando el administrador se ha editado correctamente
            return $this->redirectToRoute("inicio");
        }
        
        return $this->render("BuscadorBundle:Usuario:formularioEditar.html.twig", array(
            "form" =>$form->createView()
        ));
    }
}