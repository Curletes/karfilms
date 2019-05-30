<?php

namespace KarfilmsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use KarfilmsBundle\Entity\Sugerencia;
use KarfilmsBundle\Form\SugerenciaType;

class SugerenciaController extends Controller
{
    private $session;
    
    public function __construct()
    {
        $this->session = new Session();
    }
    
    public function mostrarSugerenciaAction(Request $request)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $sugerencia_repo = $em->getRepository("KarfilmsBundle:Sugerencia");
        $sugerencias = $sugerencia_repo->findAll();
        
        $form = $this->addSugerencia($request);
        
        return $this->render('@Karfilms/sugerencia/mostrarsugerencia.html.twig', [
            "sugerencias" => $sugerencias,
            "form" => $form->createView()
        ]);
    }
    
    public function addSugerencia($request)
    {
        $sugerencia = new Sugerencia();
        $form = $this->createForm(SugerenciaType::class, $sugerencia);
        
        $form->handleRequest($request);
        
        if($form->isSubmitted())
        {
            if($form->isValid())
            {
                $em = $this->getDoctrine()->getEntityManager();
                $sugerencia = new Sugerencia();
                $sugerencia->setTexto($form->get("texto")->getData());
                
                $em->persist($sugerencia);
                $flush = $em->flush();
                
                if($flush == null)
                {
                    $status = "Sugerencia añadida correctamente.";
                }
                else
                {
                    $status = "Error al añadir la sugerencia.";
                }
            }
            else
            {
                $status = "La sugerencia no se ha añadido porque el formulario no es válido.";
            }
            
            $this->session->getFlashBag()->add("status", $status);
        }
        
        return  $form;
    }
    
    public function eliminarSugerenciaAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $sugerencia_repo = $em->getRepository("KarfilmsBundle:Sugerencia");
        $sugerencia = $sugerencia_repo->find($id);
        
        if(count($sugerencia->getSugerenciapelicula()) == 0) 
        {
            $em->remove($sugerencia);
            $em->flush();
        }
        
        return $this->redirectToRoute("mostrar_sugerencia");
    }
    
    public function editarSugerenciaAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $sugerencia_repo = $em->getRepository("KarfilmsBundle:Sugerencia");
        $sugerencia = $sugerencia_repo->find($id);
        
        $form = $this->createForm(SugerenciaType::class, $sugerencia);
        
        $form->handleRequest($request);
        
        if($form->isSubmitted())
        {
            if($form->isValid())
            {
                $sugerencia->setTexto($form->get("texto")->getData());
                
                $em->persist($sugerencia);
                $flush = $em->flush();
                
                if($flush == null)
                {
                    $status = "Sugerencia editada correctamente.";
                }
                else
                {
                    $status = "Error al editar la sugerencia.";
                }
            }
            else
            {
                $status = "La sugerencia no se ha editado porque el formulario no es válido.";
            }
            
            $this->session->getFlashBag()->add("status", $status);
            return $this->redirectToRoute("mostrar_sugerencia");
        }
        
        return $this->render('@Karfilms/sugerencia/editarsugerencia.html.twig', [
            "form" => $form->createView()
        ]);
    }
}