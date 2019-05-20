<?php

namespace KarfilmsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use KarfilmsBundle\Entity\Pelicula;
use KarfilmsBundle\Form\PeliculaType;

class PeliculaController extends Controller
{
    private $session;
    
    public function __construct()
    {
        $this->session = new Session();
    }
    
    public function indicePeliculaAction()
    {
        $em = $this->getDoctrine()->getEntityManager();
        $pelicula_repo = $em->getRepository("KarfilmsBundle:Pelicula");
        $peliculas = $pelicula_repo->findAll();
        
        return $this->render('@Karfilms/pelicula/indicepelicula.html.twig', [
            "peliculas" => $peliculas
        ]);
    }
    
    public function addPeliculaAction(Request $request)
    {
        $pelicula = new Pelicula();
        $form = $this->createForm(PeliculaType::class, $pelicula);
        
        $form->handleRequest($request);
        
        if($form->isSubmitted())
        {
            if($form->isValid())
            {
                $em = $this->getDoctrine()->getEntityManager();
                $pelicula = new Pelicula();
                $pelicula->setTitulo($form->get("titulo")->getData());
                
                $em->persist($pelicula);
                $flush = $em->flush();
                
                if($flush == null)
                {
                    $status = "Pelicula añadido correctamente.";
                }
                else
                {
                    $status = "Error al añadir la película.";
                }
            }
            else
            {
                $status = "La pelicula no se ha añadido porque el formulario no es válido.";
            }
            
            $this->session->getFlashBag()->add("status", $status);
            return $this->redirectToRoute("indice_pelicula");
        }
        
        return $this->render('@Karfilms/pelicula/addpelicula.html.twig', [
            "form" => $form->createView()
        ]);
    }
    
    public function eliminarPeliculaAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $pelicula_repo = $em->getRepository("KarfilmsBundle:Pelicula");
        $pelicula = $pelicula_repo->find($id);
        
        if(count($pelicula->getSesion()) == 0) 
        {
            $em->remove($pelicula);
            $em->flush();
        }
        
        return $this->redirectToRoute("indice_pelicula");
    }
    
    public function editarPeliculaAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $pelicula_repo = $em->getRepository("KarfilmsBundle:Pelicula");
        $pelicula = $pelicula_repo->find($id);
        
        $form = $this->createForm(PeliculaType::class, $pelicula);
        
        $form->handleRequest($request);
        
        if($form->isSubmitted())
        {
            if($form->isValid())
            {
                $pelicula->setTitulo($form->get("titulo")->getData());
                
                $em->persist($pelicula);
                $flush = $em->flush();
                
                if($flush == null)
                {
                    $status = "Pelicula editado correctamente.";
                }
                else
                {
                    $status = "Error al editar la película.";
                }
            }
            else
            {
                $status = "La película no se ha editado porque el formulario no es válido.";
            }
            
            $this->session->getFlashBag()->add("status", $status);
            return $this->redirectToRoute("indice_pelicula");
        }
        
        return $this->render('@Karfilms/pelicula/editarpelicula.html.twig', [
            "form" => $form->createView()
        ]);
    }
}
