<?php

namespace KarfilmsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use KarfilmsBundle\Entity\Director;
use KarfilmsBundle\Form\DirectorType;

class DirectorController extends Controller
{
    private $session;
    
    public function __construct()
    {
        $this->session = new Session();
    }
    
    public function mostrarDirectorAction() {
        $em = $this->getDoctrine()->getEntityManager();
        $director_repo = $em->getRepository("KarfilmsBundle:Director");
        $directores = $director_repo->findAll();

        return $this->render('@Karfilms/director/mostrardirector.html.twig', [
                    "directores" => $directores
        ]);
    }

    public function categoriaDirectorAction($nombre) {
        $em = $this->getDoctrine()->getEntityManager();
        $director_repo = $em->getRepository('KarfilmsBundle:Director');
        $director = $director_repo->findOneBy(["nombre" => $nombre]);
        
        $peliculas_obj = $director->getDirectorpelicula();
        
        foreach($peliculas_obj as $pelicula)
        {
            $peliculas[] = $pelicula->getIdPelicula();
        }

        return $this->render('@Karfilms/director/categoriadirector.html.twig', [
                    'peliculas' => $peliculas,
                    'director' => $director
        ]);
    }
    
    public function indiceDirectorAction()
    {
        $em = $this->getDoctrine()->getEntityManager();
        $director_repo = $em->getRepository("KarfilmsBundle:Director");
        $directores = $director_repo->findAll();
        
        return $this->render('@Karfilms/director/indicedirector.html.twig', [
            "directores" => $directores
        ]);
    }
    
    public function addDirectorAction(Request $request)
    {
        $director = new Director();
        $form = $this->createForm(DirectorType::class, $director);
        
        $form->handleRequest($request);
        
        if($form->isSubmitted())
        {
            if($form->isValid())
            {
                $em = $this->getDoctrine()->getEntityManager();
                $director = new Director();
                $director->setNombre($form->get("nombre")->getData());
                
                $em->persist($director);
                $flush = $em->flush();
                
                if($flush == null)
                {
                    $status = "Director añadido correctamente.";
                }
                else
                {
                    $status = "Error al añadir el director.";
                }
            }
            else
            {
                $status = "El director no se ha añadido porque el formulario no es válido.";
            }
            
            $this->session->getFlashBag()->add("status", $status);
            return $this->redirectToRoute("indice_director");
        }
        
        return $this->render('@Karfilms/director/adddirector.html.twig', [
            "form" => $form->createView()
        ]);
    }
    
    public function eliminarDirectorAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $director_repo = $em->getRepository("KarfilmsBundle:Director");
        $director = $director_repo->find($id);
        
        if(count($director->getDirectorpelicula()) == 0) 
        {
            $em->remove($director);
            $em->flush();
        }
        
        return $this->redirectToRoute("indice_director");
    }
    
    public function editarDirectorAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $director_repo = $em->getRepository("KarfilmsBundle:Director");
        $director = $director_repo->find($id);
        
        $form = $this->createForm(DirectorType::class, $director);
        
        $form->handleRequest($request);
        
        if($form->isSubmitted())
        {
            if($form->isValid())
            {
                $director->setNombre($form->get("nombre")->getData());
                
                $em->persist($director);
                $flush = $em->flush();
                
                if($flush == null)
                {
                    $status = "Director editado correctamente.";
                }
                else
                {
                    $status = "Error al editar el director.";
                }
            }
            else
            {
                $status = "El director no se ha editado porque el formulario no es válido.";
            }
            
            $this->session->getFlashBag()->add("status", $status);
            return $this->redirectToRoute("indice_director");
        }
        
        return $this->render('@Karfilms/director/editardirector.html.twig', [
            "form" => $form->createView()
        ]);
    }
}