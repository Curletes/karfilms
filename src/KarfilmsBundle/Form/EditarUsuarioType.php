<?php

namespace KarfilmsBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class EditarUsuarioType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('nombre', TextType::class, array("label" => "Nombre de usuario:", "required"=>"required"))
                ->add('email', EmailType::class, array("label" => "Email:", "required"=>"required"))
                ->add('password', RepeatedType::class, array('type' => PasswordType::class,
                    'invalid_message' => 'Las contraseñas no coinciden.',
                    'options' => ['attr' => ['class' => 'password-field']],
                                'required' => false,
                                'first_options'  => ['label' => 'Cambiar contraseña:'],
                                'second_options' => ['label' => 'Repite la nueva contraseña:']))
                ->add('Enviar', SubmitType::class);
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'KarfilmsBundle\Entity\Usuario'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'karfilmsbundle_usuario';
    }
}
