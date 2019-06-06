<?php

namespace KarfilmsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use KarfilmsBundle\Entity\Asiento;
use KarfilmsBundle\Form\AsientoType;

class AsientoController extends Controller 
{
    private $session;

    public function __construct() {
        $this->session = new Session();
    }
    
    public function indiceAsientoAction()
    {
        $em = $this->getDoctrine()->getEntityManager();
        $asiento_repo = $em->getRepository("KarfilmsBundle:Asiento");
        $asientos = $asiento_repo->findBy([],['idSala' => 'ASC', 'fila' => 'ASC', 'butaca' => 'ASC']);
        
        $asientoreservado_repo = $em->getRepository("KarfilmsBundle:Asientoreservado");
        $asientosreservados = $asientoreservado_repo->findAll();
        
        return $this->render('@Karfilms/asiento/indiceasiento.html.twig', [
            "asientos" => $asientos,
            "asientosreservados" => $asientosreservados
        ]);
    }
    
    public function addAsientoAction(Request $request)
    {
        $asiento = new Asiento();
        $form = $this->createForm(AsientoType::class, $asiento);
        
        $form->handleRequest($request);
        
        if($form->isSubmitted())
        {
            if($form->isValid())
            {
                $em = $this->getDoctrine()->getEntityManager();
                
                $asiento_repo = $em->getRepository("KarfilmsBundle:Asiento");
                $asiento_existe = $asiento_repo->findOneBy([
                    "idSala" => $form->get("idSala")->getData(),
                    "fila" => $form->get("fila")->getData(),
                    "butaca" => $form->get("butaca")->getData()
                ]);
                
                if ($asiento_existe == null) {
                    $asiento = new Asiento();
                    $asiento->setFila($form->get("fila")->getData());
                    $asiento->setButaca($form->get("butaca")->getData());
                    $asiento->setIdSala($form->get("idSala")->getData());

                    $em->persist($asiento);
                    $flush = $em->flush();

                    if($flush == null)
                    {
                        $status = "Asiento añadido correctamente.";
                    }
                }
                else
                {
                    $status = "Error ese asiento ya existe.";
                }
            }
            else
            {
                $status = "El asiento no se ha añadido porque el formulario no es válido.";
            }
            
            $this->session->getFlashBag()->add("status", $status);
            return $this->redirectToRoute("indice_asiento");
        }
        
        return $this->render('@Karfilms/asiento/addasiento.html.twig', [
            "form" => $form->createView()
        ]);
    }
    
    public function eliminarAsientoAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $asiento_repo = $em->getRepository("KarfilmsBundle:Asiento");
        $asiento = $asiento_repo->find($id);
        
        $sala_repo = $em->getRepository("KarfilmsBundle:Sala");
        $salas = $sala_repo->findAll();
        
        foreach($salas as $sala)
        {
            if($asiento->getIdSala()->getId() != $sala->getId()) 
            {
                $em->remove($asiento);
                $em->flush();
            }
        }
        
        return $this->redirectToRoute("indice_asiento");
    }
    
    public function editarAsientoAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $asiento_repo = $em->getRepository("KarfilmsBundle:Asiento");
        $asiento = $asiento_repo->find($id);
        
        $form = $this->createForm(AsientoType::class, $asiento);
        
        $form->handleRequest($request);
        
        if($form->isSubmitted())
        {
            if($form->isValid())
            {
                $asiento_repo = $em->getRepository("KarfilmsBundle:Asiento");
                $asiento_existe = $asiento_repo->findOneBy([
                    "idSala" => $form->get("idSala")->getData(),
                    "fila" => $form->get("fila")->getData(),
                    "butaca" => $form->get("butaca")->getData()
                ]);
                
                if ($asiento_existe == null) {
                    $asiento = new Asiento();
                    $asiento->setFila($form->get("fila")->getData());
                    $asiento->setButaca($form->get("butaca")->getData());
                    $asiento->setIdSala($form->get("idSala")->getData());

                    $em->persist($asiento);
                    $flush = $em->flush();

                    if($flush == null)
                    {
                        $status = "Asiento añadido correctamente.";
                    }
                }
                else
                {
                    $status = "Ese asiento ya existe.";
                }
            }
            else
            {
                $status = "El asiento no se ha editada porque el formulario no es válido.";
            }
            
            $this->session->getFlashBag()->add("status", $status);
            return $this->redirectToRoute("indice_asiento");
        }
        
        return $this->render('@Karfilms/asiento/editarasiento.html.twig', [
            "form" => $form->createView()
        ]);
    }
}
