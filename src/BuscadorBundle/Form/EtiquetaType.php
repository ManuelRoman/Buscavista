<?php

namespace BuscadorBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class EtiquetaType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('nombre', TextType::class, array(
            "label"=>"Nombre",
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
            'data_class' => 'BuscadorBundle\Entity\Etiqueta'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'buscadorbundle_etiqueta';
    }


}
