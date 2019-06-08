<?php

namespace KarfilmsBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ReservarAsientoType extends AbstractType {

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('fila', EntityType::class, array(
                    "label" => "Fila:",
                    "required" => "required",
                    "mapped" => false,
                    "class" => "KarfilmsBundle:Asiento",
                    "choice_label" => "fila"
                ))
                ->add('butaca', EntityType::class, array(
                    "label" => "Butaca:",
                    "required" => "required",
                    "mapped" => false,
                    "class" => "KarfilmsBundle:Asiento",
                    "choice_label" => "butaca"
                ))
                ->add('Enviar', SubmitType::class);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'KarfilmsBundle\Entity\Asiento'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix() {
        return 'karfilmsbundle_asiento';
    }

}
