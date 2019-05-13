<?php

namespace KarfilmsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        /*$em = $this->getDoctrine()->getEntityManager();
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
        }*/
        
        /*$em = $this->getDoctrine()->getEntityManager();
        $genero_repo = $em->getRepository("KarfilmsBundle:Genero");
        $generos = $genero_repo->findAll();
        
        foreach ($generos as $genero) {
            echo $genero->getNombre()."<br>";
            
            $Generopelicula = $genero->getGeneropelicula();
            
            foreach ($Generopelicula as $pelicula) {
                echo $pelicula->getIdPelicula()->getTitulo().", ";
            }
            echo "<br><hr>";
        }*/
        
        /*$em = $this->getDoctrine()->getEntityManager();
        $director_repo = $em->getRepository("KarfilmsBundle:Director");
        $directores = $director_repo->findAll();
        
        foreach ($directores as $director) {
            echo $director->getNombre()."<br>";
            
            $Directorpelicula = $director->getDirectorpelicula();
            
            foreach ($Directorpelicula as $pelicula) {
                echo $pelicula->getIdPelicula()->getTitulo().", ";
            }
            echo "<br><hr>";
        }*/
        
        $em = $this->getDoctrine()->getEntityManager();
        $actor_repo = $em->getRepository("KarfilmsBundle:Actor");
        $actores = $actor_repo->findAll();
        
        foreach ($actores as $actor) {
            echo $actor->getNombre()."<br>";
            
            $Actorpelicula = $actor->getActorpelicula();
            
            foreach ($Actorpelicula as $pelicula) {
                echo $pelicula->getIdPelicula()->getTitulo().", ";
            }
            echo "<br><hr>";
        }
        
        die();
        //return $this->render('Default/index.html.twig');
    }
}