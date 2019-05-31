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
    
    public function mostrarYaddSugerenciaAction(Request $request, $id = null)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $sugerencia_repo = $em->getRepository("KarfilmsBundle:Sugerencia");
        $sugerencias = $sugerencia_repo->findAll();
        
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
                
                $usuario = $this->getUser();
                $sugerencia->setIdUsuario($usuario);
                
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
            return $this->redirectToRoute('mostrar_sugerencia');
        }
        
        return $this->render('@Karfilms/sugerencia/mostrarsugerencia.html.twig', [
            "sugerencias" => $sugerencias,
            "form" => $form->createView()
        ]);
    }

    public function eliminarSugerenciaAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $sugerencia_repo = $em->getRepository("KarfilmsBundle:Sugerencia");
        $sugerencia = $sugerencia_repo->find($id);
        
        $em->remove($sugerencia);
        $em->flush();
        
        return $this->redirectToRoute("mostrar_sugerencia");
    }
}