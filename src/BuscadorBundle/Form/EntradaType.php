<?php

namespace BuscadorBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class EntradaType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('titulo', TextType::class, array(
                "label"=>"Título",
                "required"=>"required",
                "attr" => array(
                    "class" =>"form-name form-control"))
                )
            ->add('contenido', TextareaType::class, array(
                "label"=>"Contenido:",
                "required"=>"required",
                "attr" => array("class" =>"form-name form-control"))
                )
            ->add('imagen1', FileType::class, array(
                "label"=>"Imagen:",
                "required"=>false,
                "attr" => array("id" =>"btn-imagen"),
                //No exije que la imagen sea un fichero, puede ser un string, se usa para poder editar la entrada
                "data_class" =>null)
                )
             ->add('latitud', TextType::class, array(
                "label"=>"Latitud",
                "required"=>"required",
                "attr" => array(
                    "class" =>"form-name form-control"))
                )
             ->add('longitud', TextType::class, array(
                "label"=>"Longitud",
                "required"=>"required",
                "attr" => array(
                "class" =>"form-name form-control"))
                )
             ->add('etiquetas', TextType::class, array(
                //el atributo no debe estar mapeado con doctrine
                "mapped" => false,
                "label"=>"Etiquetas:",
                "required"=>"required",
                "attr" => array("class" =>"form-name form-control"))
                 )
             ->add('preferencia', TextType::class, array(
                "label"=>"Preferencia",
                "required"=>"required",
                "attr" => array(
                "class" =>"form-name form-control"))
                )
             ->add('Guardar', SubmitType::class, array(
                "attr" => array("class" =>"btn btn-default"))
                );
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'BuscadorBundle\Entity\Entrada'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'buscadorbundle_entrada';
    }


}
