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
                    $etiqueta->setNombre($form->get("nombre")->getData());
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
}