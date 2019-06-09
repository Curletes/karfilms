<?php

namespace KarfilmsBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Doctrine\ORM\EntityRepository;

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
                    "choice_label" => "fila",
                    "query_builder" => function (EntityRepository $er) use ($options){
                        return $er->createQueryBuilder('a')
                                ->orderBy('a.fila', 'ASC')
                                ->where('a.idSala = :idSala')
                                ->groupBy("a.fila")
                                ->setParameter("idSala", $options["idSala"]);
                    },
                ))
                ->add('butaca', EntityType::class, array(
                    "label" => "Butaca:",
                    "required" => "required",
                    "mapped" => false,
                    "class" => "KarfilmsBundle:Asiento",
                    "choice_label" => "butaca",
                    "query_builder" => function (EntityRepository $er) use ($options){
                        return $er->createQueryBuilder('a')
                                ->orderBy('a.butaca', 'ASC')
                                ->where('a.idSala = :idSala')
                                ->groupBy("a.butaca")
                                ->setParameter("idSala", $options["idSala"]);
                    },
                ))
                ->add('Comprar', SubmitType::class);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'idSala' => null,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix() {
        return 'karfilmsbundle_asiento';
    }

}
