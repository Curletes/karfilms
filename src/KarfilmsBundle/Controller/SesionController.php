<?php

namespace KarfilmsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use KarfilmsBundle\Entity\Sesion;
use KarfilmsBundle\Form\SesionType;

class SesionController extends Controller
{
    private $session;
    
    public function __construct()
    {
        $this->session = new Session();
    }
    
    public function indiceSesionAction()
    {
        $em = $this->getDoctrine()->getEntityManager();
        $sesion_repo = $em->getRepository("KarfilmsBundle:Sesion");
        $sesiones = $sesion_repo->findAll();
                
        return $this->render('@Karfilms/sesion/indicesesion.html.twig', [
            "sesiones" => $sesiones,
        ]);
    }
    
    public function addSesionAction(Request $request)
    {
        $sesion = new Sesion();
        $form = $this->createForm(SesionType::class, $sesion);
        
        $form->handleRequest($request);
        
        if($form->isSubmitted())
        {
            if($form->isValid())
            {
                $em = $this->getDoctrine()->getEntityManager();
                
                $sesion_repo = $em->getRepository("KarfilmsBundle:Sesion");
                $sesion_ocupada = $sesion_repo->findOneBy([
                    "idSala" => $form->get("idSala")->getData(),
                    "horarios" => $form->get("horarios")->getData()
                ]);
                
                if ($sesion_ocupada == null) {
                    $sesion = new Sesion();
                    $sesion->setHorarios($form->get("horarios")->getData());
                    $sesion->setIdPelicula($form->get("idPelicula")->getData());
                    $sesion->setIdSala($form->get("idSala")->getData());
                    
                    $em->persist($sesion);
                    $flush = $em->flush();
                
                    if($flush == null)
                    {
                        $status = "Sesión añadida correctamente.";
                    }
                }
                else
                {
                    $status = "Esa sesión ya está ocupada.";
                }
            }
            else
            {
                $status = "La sesión no se ha añadida porque el formulario no es válido.";
            }
            
            $this->session->getFlashBag()->add("status", $status);
            return $this->redirectToRoute("indice_sesion");
        }
        
        return $this->render('@Karfilms/sesion/addsesion.html.twig', [
            "form" => $form->createView()
        ]);
    }
    
    public function eliminarSesionAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $sesion_repo = $em->getRepository("KarfilmsBundle:Sesion");
        $sesion = $sesion_repo->find($id);
        
        $em->remove($sesion);
        $em->flush();
        
        return $this->redirectToRoute("indice_sesion");
    }
    
    public function editarSesionAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $sesion_repo = $em->getRepository("KarfilmsBundle:Sesion");
        $sesion = $sesion_repo->find($id);
        
        $form = $this->createForm(SesionType::class, $sesion);
        
        $form->handleRequest($request);
        
        if($form->isSubmitted())
        {
            if($form->isValid())
            {
                $sesion_repo = $em->getRepository("KarfilmsBundle:Sesion");
                $sesion_ocupada = $sesion_repo->findOneBy([
                    "idSala" => $form->get("idSala")->getData(),
                    "horarios" => $form->get("horarios")->getData()
                ]);
                
                if ($sesion_ocupada == null) {
                    $sesion = new Sesion();
                    $sesion->setHorarios($form->get("horarios")->getData());
                    $sesion->setIdPelicula($form->get("idPelicula")->getData());
                    $sesion->setIdSala($form->get("idSala")->getData());
                    
                    $em->persist($sesion);
                    $flush = $em->flush();
                
                    if($flush == null)
                    {
                        $status = "Sesión añadida correctamente.";
                    }
                }
                else
                {
                    $status = "Esa sesión ya está ocupada.";
                }
            }
            else
            {
                $status = "La sesión no se ha editado porque el formulario no es válido.";
            }
            
            $this->session->getFlashBag()->add("status", $status);
            return $this->redirectToRoute("indice_sesion");
        }
        
        return $this->render('@Karfilms/sesion/editarsesion.html.twig', [
            "form" => $form->createView()
        ]);
    }
    
    public function reservarEntradaAction(Request $request, $pelicula, $sesion)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $pelicula_repo = $em->getRepository("KarfilmsBundle:Pelicula");
        $pelicula = $pelicula_repo->find(["id" => $pelicula]);
        
        $sesion_repo = $em->getRepository("KarfilmsBundle:Sesion");
        $sesion = $sesion_repo->find(["id" => $sesion]);
        
        return $this->render('@Karfilms/reserva/reservarentrada.html.twig', [
            "pelicula" => $pelicula,
            "sesion" => $sesion
        ]);
    }
}