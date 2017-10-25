<?php

namespace BuscadorBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use BuscadorBundle\Entity\Usuario;
use BuscadorBundle\Form\UsuarioType;
use Symfony\Component\HttpFoundation\Session\Session;

class UsuarioController extends Controller
{
    private $session;
    //Lo declaramos y creamos a nivel de clase y lo tenemos para todos los métodos
    public function __construct(){
        $this->session = new Session();
    }
    
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
    
    public function inicioAction(Request $request)
    {
        return $this->render('BuscadorBundle:Usuario:inicio.html.twig');
    }
    
    public function  creaAdminAction(Request $request){
        $usuario = new Usuario();
        $form = $this->createForm(UsuarioType::class, $usuario);
        $form->handleRequest($request);
        if($form->isSubmitted()){
            if($form->isValid()){
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
                $this->session->getFlashBag()->add("status", $status);
                return $this->redirectToRoute("inicio");
            }
        }
        
        return $this->render("BuscadorBundle:Usuario:crear.html.twig", array(
            "form" =>$form->createView()
        ));
        
    }
    
    /**
     * Método sin paginador
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
    
    public function listarAdminsAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        //Realizamos la consulta a bbdd
        $dql   = "SELECT u FROM BuscadorBundle:Usuario u";
        $query = $em->createQuery($dql);
        
        //creamos el $paginator que llama el método get de KnpPaginatorBundle
        $paginator  = $this->get('knp_paginator');
        //le pasamos a $paginator los parámetros y los asignamos a $pagination
        $pagination = $paginator->paginate(
            $query, //origen de los datos
            $request->query->get('page', 1), //número de página por la que empieza
            3 // límite de resultados por página
            );
        return $this->render('BuscadorBundle:Usuario:eliminarEditarPaginador.html.twig', array(
            'pagination' => $pagination
        ));
    }
    
    public function eliminarAdminAction(Request $request, $id){
        $em = $this->getDoctrine()->getManager();
        $usuario_repository=$em->getRepository("BuscadorBundle:Usuario");
        $usuario = $usuario_repository->find($id);
        $em->remove($usuario);
        $flush = $em->flush();
        if($flush==null){
            $status = "El administrador se ha eliminado correctamente.";
        }else{
            $status = "Error al eliminar el administrador.";
        }
        $this->session->getFlashBag()->add("status", $status);
        return $this->render("BuscadorBundle:Usuario:inicio.html.twig");
    }
    
    public function editarAdminAction(Request $request, $id){
        $em = $this->getDoctrine()->getManager();
        $usuario_repository=$em->getRepository("BuscadorBundle:Usuario");
        $usuario = $usuario_repository->find($id);
        
        //Creamos el formularío y le damos el usuario, nos rellena los campos en la vista
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
                $em->persist($usuario);
                $flush = $em->flush();
                
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