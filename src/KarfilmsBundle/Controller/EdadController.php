<?php

namespace KarfilmsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use KarfilmsBundle\Entity\Edad;
use KarfilmsBundle\Form\EdadType;

class EdadController extends Controller
{
    private $session;
    
    public function __construct()
    {
        $this->session = new Session();
    }
    
    public function mostrarEdadAction() {
        $em = $this->getDoctrine()->getEntityManager();
        $edad_repo = $em->getRepository("KarfilmsBundle:Edad");
        $edades = $edad_repo->findAll();

        return $this->render('@Karfilms/edad/mostraredad.html.twig', [
                    "edades" => $edades
        ]);
    }

    public function categoriaEdadAction(Request $request, $clasificacion) {
        $em = $this->getDoctrine()->getEntityManager();
        
        $edad_repo = $em->getRepository('KarfilmsBundle:Edad');
        $edad = $edad_repo->findOneBy(["clasificacion" => $clasificacion]);
        $idEdad = $edad->getId();
        $pelicula_repo = $em->getRepository("KarfilmsBundle:Pelicula");
        $peliculas = $pelicula_repo->findBy(["idEdad" => $idEdad]);
 
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
                $peliculas,
                $request->query->getInt('page', 1),
                5
        );

        if (isset($peliculas)) {
            return $this->render('@Karfilms/edad/categoriaedad.html.twig', [
                        'edad' => $edad,
                        "pagination" => $pagination
            ]);
        } else {
            return $this->render('@Karfilms/edad/categoriaedad.html.twig', [
                        'edad' => $edad
            ]);
        }
    }
    
    public function indiceEdadAction()
    {
        $em = $this->getDoctrine()->getEntityManager();
        $edad_repo = $em->getRepository("KarfilmsBundle:Edad");
        $edades = $edad_repo->findAll();
        
        $pelicula_repo = $em->getRepository("KarfilmsBundle:Pelicula");
        $peliculas = $pelicula_repo->findAll();
        
        return $this->render('@Karfilms/edad/indiceedad.html.twig', [
            "edades" => $edades,
            "peliculas" => $peliculas
        ]);
    }
    
    public function addEdadAction(Request $request)
    {
        $edad = new Edad();
        $form = $this->createForm(EdadType::class, $edad);
        
        $form->handleRequest($request);
        
        if($form->isSubmitted())
        {
            if($form->isValid())
            {
                $em = $this->getDoctrine()->getEntityManager();
                $edad = new Edad();
                $edad->setClasificacion($form->get("clasificacion")->getData());
                
                $em->persist($edad);
                $flush = $em->flush();
                
                if($flush == null)
                {
                    $status = "Edad añadida correctamente.";
                }
                else
                {
                    $status = "Error al añadir la edad.";
                }
            }
            else
            {
                $status = "El edad no se ha añadida porque el formulario no es válido.";
            }
            
            $this->session->getFlashBag()->add("status", $status);
            return $this->redirectToRoute("indice_edad");
        }
        
        return $this->render('@Karfilms/edad/addedad.html.twig', [
            "form" => $form->createView()
        ]);
    }
    
    public function eliminarEdadAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $edad_repo = $em->getRepository("KarfilmsBundle:Edad");
        $edad = $edad_repo->find($id);
        
        $pelicula_repo = $em->getRepository("KarfilmsBundle:Pelicula");
        $peliculas = $pelicula_repo->findAll();
        
        foreach($peliculas as $pelicula)
        {
            if($pelicula->getIdEdad()->getId() != $edad->getId()) 
            {
                $em->remove($edad);
                $em->flush();
            }
        }
        
        return $this->redirectToRoute("indice_edad");
    }
    
    public function editarEdadAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $edad_repo = $em->getRepository("KarfilmsBundle:Edad");
        $edad = $edad_repo->find($id);
        
        $form = $this->createForm(EdadType::class, $edad);
        
        $form->handleRequest($request);
        
        if($form->isSubmitted())
        {
            if($form->isValid())
            {
                $edad->setClasificacion($form->get("clasificacion")->getData());
                
                $em->persist($edad);
                $flush = $em->flush();
                
                if($flush == null)
                {
                    $status = "Edad editada correctamente.";
                }
                else
                {
                    $status = "Error al editar la edad.";
                }
            }
            else
            {
                $status = "El edad no se ha editada porque el formulario no es válido.";
            }
            
            $this->session->getFlashBag()->add("status", $status);
            return $this->redirectToRoute("indice_edad");
        }
        
        return $this->render('@Karfilms/edad/editaredad.html.twig', [
            "form" => $form->createView()
        ]);
    }
}