<?php

namespace UsuariosBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use UsuariosBundle\Entity\Usuario;
use UsuariosBundle\Form\UsuarioType;
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
        return $this->render('UsuariosBundle:Usuario:login.html.twig', array(
            "last_username" => $lastUsername
        ));
    }
    
    public function inicioAction(Request $request)
    {
        return $this->render('UsuariosBundle:Usuario:inicio.html.twig');
    }
    
    public function  creaAdminAction(Request $request){
        $usuario = new Usuario();
        $form = $this->createForm(UsuarioType::class, $usuario);
        $form->handleRequest($request);
        if($form->isSubmitted()){
            if($form->isValid()){
                $em = $this->getDoctrine()->getManager();
                //Comprobamos si el email ya esta registrado
                $usuario_repository=$em->getRepository("UsuariosBundle:Usuario");
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
        
        return $this->render("UsuariosBundle:Usuario:crear.html.twig", array(
            "form" =>$form->createView()
        ));
        
    }
    
}