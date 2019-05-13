<?php

namespace KarfilmsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        $em = $this->getDoctrine()->getEntityManager();
        $pelicula_repo = $em->getRepository("KarfilmsBundle:Pelicula");
        $peliculas = $pelicula_repo->findAll();
        
        foreach ($peliculas as $pelicula) {
            echo $pelicula->getTitulo()."<br>";
            echo $pelicula->getSinopsis()."<br>";
            echo $pelicula->getDuracion()."<br>";
            echo $pelicula->getIdEdad()->getClasificacion()."<br>";

            $generos = $pelicula->getGeneropelicula();
            $actores = $pelicula->getActorpelicula();
            $directores = $pelicula->getDirectorpelicula();
            
            foreach ($generos as $genero) {
                echo $genero->getIdGenero()->getNombre().", ";
            }
            echo "<br>";
            foreach ($actores as $actor) {
                echo $actor->getIdActor()->getNombre().", ";
            }
            echo "<br>";
            foreach ($directores as $director) {
                echo $director->getIdDirector()->getNombre().", ";
            }
            
            echo "<hr>";
        }

        die();
        //return $this->render('Default/index.html.twig');
    }
}