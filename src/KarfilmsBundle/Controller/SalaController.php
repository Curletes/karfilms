<?php

namespace KarfilmsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use KarfilmsBundle\Entity\Sala;
use KarfilmsBundle\Form\SalaType;

class SalaController extends Controller
{
    private $session;
    
    public function __construct()
    {
        $this->session = new Session();
    }
    
    public function indiceSalaAction()
    {
        $em = $this->getDoctrine()->getEntityManager();
        $sala_repo = $em->getRepository("KarfilmsBundle:Sala");
        $salas = $sala_repo->findAll();
        
        $sesion_repo = $em->getRepository("KarfilmsBundle:Sesion");
        $sesiones = $sesion_repo->findAll();
        
        return $this->render('@Karfilms/sala/indicesala.html.twig', [
            "salas" => $salas,
            "sesiones" => $sesiones
        ]);
    }
    
    public function addSalaAction(Request $request)
    {
        $sala = new Sala();
        $form = $this->createForm(SalaType::class, $sala);
        
        $form->handleRequest($request);
        
        if($form->isSubmitted())
        {
            if($form->isValid())
            {
                $em = $this->getDoctrine()->getEntityManager();
                $sala = new Sala();
                $sala->setNombre($form->get("nombre")->getData());
                
                $em->persist($sala);
                $flush = $em->flush();
                
                if($flush == null)
                {
                    $status = "Sala añadida correctamente.";
                }
                else
                {
                    $status = "Error al añadir la sala.";
                }
            }
            else
            {
                $status = "El sala no se ha añadida porque el formulario no es válido.";
            }
            
            $this->session->getFlashBag()->add("status", $status);
            return $this->redirectToRoute("indice_sala");
        }
        
        return $this->render('@Karfilms/sala/addsala.html.twig', [
            "form" => $form->createView()
        ]);
    }
    
    public function eliminarSalaAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $sala_repo = $em->getRepository("KarfilmsBundle:Sala");
        $sala = $sala_repo->find($id);
        
        $pelicula_repo = $em->getRepository("KarfilmsBundle:Pelicula");
        $peliculas = $pelicula_repo->findAll();
        
        foreach($peliculas as $pelicula)
        {
            if($pelicula->getIdSala()->getId() != $sala->getId()) 
            {
                $em->remove($sala);
                $em->flush();
            }
        }
        
        return $this->redirectToRoute("indice_sala");
    }
    
    public function editarSalaAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $sala_repo = $em->getRepository("KarfilmsBundle:Sala");
        $sala = $sala_repo->find($id);
        
        $form = $this->createForm(SalaType::class, $sala);
        
        $form->handleRequest($request);
        
        if($form->isSubmitted())
        {
            if($form->isValid())
            {
                $sala->setNombre($form->get("nombre")->getData());
                
                $em->persist($sala);
                $flush = $em->flush();
                
                if($flush == null)
                {
                    $status = "Sala editada correctamente.";
                }
                else
                {
                    $status = "Error al editar la sala.";
                }
            }
            else
            {
                $status = "El sala no se ha editada porque el formulario no es válido.";
            }
            
            $this->session->getFlashBag()->add("status", $status);
            return $this->redirectToRoute("indice_sala");
        }
        
        return $this->render('@Karfilms/sala/editarsala.html.twig', [
            "form" => $form->createView()
        ]);
    }
}