<?php

namespace KarfilmsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller 
{
    public function pruebaAction() {
        /* $em = $this->getDoctrine()->getEntityManager();
          $genero_repo = $em->getRepository("KarfilmsBundle:Genero");
          $generos = $genero_repo->findAll();

          foreach ($generos as $genero) {
          echo $genero->getNombre()."<br>";

          $Generopelicula = $genero->getGeneropelicula();

          foreach ($Generopelicula as $pelicula) {
          echo $pelicula->getIdPelicula()->getTitulo().", ";
          }
          echo "<br><hr>";
          } */

        /* $em = $this->getDoctrine()->getEntityManager();
          $director_repo = $em->getRepository("KarfilmsBundle:Director");
          $directores = $director_repo->findAll();

          foreach ($directores as $director) {
          echo $director->getNombre()."<br>";

          $Directorpelicula = $director->getDirectorpelicula();

          foreach ($Directorpelicula as $pelicula) {
          echo $pelicula->getIdPelicula()->getTitulo().", ";
          }
          echo "<br><hr>";
          } */

        /* $em = $this->getDoctrine()->getEntityManager();
          $actor_repo = $em->getRepository("KarfilmsBundle:Actor");
          $actores = $actor_repo->findAll();

          foreach ($actores as $actor) {
          echo $actor->getNombre()."<br>";

          $Actorpelicula = $actor->getActorpelicula();

          foreach ($Actorpelicula as $pelicula) {
          echo $pelicula->getIdPelicula()->getTitulo().", ";
          }
          echo "<br><hr>";
          } */

        /* $em = $this->getDoctrine()->getEntityManager();
          $edad_repo = $em->getRepository("KarfilmsBundle:Edad");
          $edades = $edad_repo->findAll();

          foreach ($edades as $edad) {
          echo $edad->getClasificacion()."<br>";

          $peliculas = $edad->getPeliculas();

          foreach ($peliculas as $pelicula) {
          echo $pelicula->getTitulo().", ";
          }
          echo "<br><hr>";
          } */

        /* $em = $this->getDoctrine()->getEntityManager();
          $pelicula_repo = $em->getRepository("KarfilmsBundle:Pelicula");
          $peliculas = $pelicula_repo->findAll();

          foreach ($peliculas as $pelicula) {
          echo $pelicula->getTitulo()."<br>";

          $sesiones = $pelicula->getSesiones();

          foreach ($sesiones as $sesion) {
          echo $sesion->getHorarios()->format("Y-m-d H:i:s")." - ";
          echo $sesion->getIdSala()->getNombre()."<br>";
          }
          echo "<br><hr>";
          } */

        /* $em = $this->getDoctrine()->getEntityManager();
          $sala_repo = $em->getRepository("KarfilmsBundle:Sala");
          $salas = $sala_repo->findAll();

          foreach ($salas as $sala) {
          echo $sala->getNombre()."<br>";

          $asientos = $sala->getAsientos();

          foreach ($asientos as $asiento) {
          echo $asiento->getFila()." - ";
          echo $asiento->getButaca()."<br>";
          }
          echo "<br><hr>";
          }

          return $this->render('Default/index.html.twig'); */
    }

    public function indexAction() {
        $em = $this->getDoctrine()->getEntityManager();
        $pelicula_repo = $em->getRepository("KarfilmsBundle:Pelicula");
        $peliculas = $pelicula_repo->findAll();

        foreach ($peliculas as $pelicula) {
            $pelicula->getTitulo();
            $pelicula->getSinopsis();
            $pelicula->getDuracion();
            $pelicula->getIdEdad()->getClasificacion();

            $generos = $pelicula->getGeneropelicula();
            $actores = $pelicula->getActorpelicula();
            $directores = $pelicula->getDirectorpelicula();

            foreach ($generos as $genero) {
                $genero->getIdGenero()->getNombre();
            }

            foreach ($actores as $actor) {
                $actor->getIdActor()->getNombre();
            }

            foreach ($directores as $director) {
                $director->getIdDirector()->getNombre();
            }
        }

        return $this->render('@Karfilms/Default/index.html.twig');
    }

}
