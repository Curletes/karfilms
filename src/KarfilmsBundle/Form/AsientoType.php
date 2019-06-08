<?php

namespace KarfilmsBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class AsientoType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('fila', NumberType::class, array("label" => "Fila:", "required"=>"required"))
                ->add('butaca', NumberType::class, array("label" => "Butaca:", "required"=>"required"))
                ->add('idSala', EntityType::class, array("label" => "Sala:", "required"=>"required", "mapped" => false, "class" => "KarfilmsBundle:Sala"))
                ->add('Enviar', SubmitType::class);
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'KarfilmsBundle\Entity\Asiento'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'karfilmsbundle_asiento';
    }


}
