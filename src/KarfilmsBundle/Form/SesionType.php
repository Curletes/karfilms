<?php

namespace KarfilmsBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class SesionType extends AbstractType {

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('horarios', DateTimeType::class, array("label" => "Fecha y hora:", "required" => "required", 'placeholder' => [
                        'year' => 'Año', 'month' => 'Mes', 'day' => 'Día',
                        'hour' => 'Hora', 'minute' => 'Minutos',
            ]))
                ->add('idPelicula', EntityType::class, array("label" => "Película:", "required" => "required", "mapped" => false, "class" => "KarfilmsBundle:Pelicula"))
                ->add('idSala', EntityType::class, array("label" => "Sala:", "required" => "required", "mapped" => false, "class" => "KarfilmsBundle:Sala"))
                ->add('Enviar', SubmitType::class);
    }

/**
     * {@inheritdoc}
     */

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'KarfilmsBundle\Entity\Sesion'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix() {
        return 'karfilmsbundle_sesion';
    }

}
