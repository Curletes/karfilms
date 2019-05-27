<?php

namespace KarfilmsBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class PeliculaType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('titulo', TextType::class, array("label" => "Título:", "required"=>"required"))
                ->add('sinopsis', TextareaType::class, array("label" => "Sinopsis:", "required"=>"required"))
                ->add('cartel', FileType::class, array("label" => "Cartel:", "required"=>"required"))
                ->add('trailer', FileType::class, array("label" => "Trailer:", "required"=>"required"))
                ->add('duracion', NumberType::class, array("label" => "Duración:", "required"=>"required"))
                ->add('idEdad', EntityType::class, array("label" => "Clasificación por edades:", "required"=>"required", "mapped" => false, "class" => "KarfilmsBundle:Edad"))
                ->add('actores', TextType::class, array("label" => "Actores principales:", "required"=>"required", "mapped" => false))
                ->add('directores', TextType::class, array("label" => "Directores:", "required"=>"required", "mapped" => false))
                ->add('generos', TextType::class, array("label" => "Géneros:", "required"=>"required", "mapped" => false))
                ->add('Enviar', SubmitType::class);
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'KarfilmsBundle\Entity\Pelicula'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'karfilmsbundle_pelicula';
    }
}
