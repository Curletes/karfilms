<?php

namespace KarfilmsBundle\Repository;

use KarfilmsBundle\Entity\Actor;
use KarfilmsBundle\Entity\Actorpelicula;
use KarfilmsBundle\Entity\Director;
use KarfilmsBundle\Entity\Directorpelicula;
use KarfilmsBundle\Entity\Genero;
use KarfilmsBundle\Entity\Generopelicula;

class PeliculaRepository extends \Doctrine\ORM\EntityRepository {

    /*
     * Función para asignar actores a una película. Este método es llamado desde
     * la función para añadir y editar películas en el controlador correspondiente.
     */
    public function guardarActoresPelicula($actores = null, $titulo = null, $pelicula = null) {
        $em = $this->getEntityManager();

        $actor_repo = $em->getRepository("KarfilmsBundle:Actor");

        if ($pelicula == null) {
            $pelicula = $this->findOneBy([
                "titulo" => $titulo,
            ]);
        }

        /*
         * Se crea un array con todos los actores que se han escrito, separándolos por
         * las comas.
         */
        $actores .= ",";
        $actores = explode(",", $actores);

        foreach ($actores as $actor) {
            /*
             * Se comprueba que el actor introducido no esté vacío.
             */
            if (strlen($actor) > 0) {
                /*
                 * Si el actor tiene espacios antes o después de su nombre, estos
                 * se eliminan y se comprueba si el actor ya existe.
                 */
                $actor = trim($actor);
                $isset_actor = $actor_repo->findOneBy(["nombre" => $actor]);

                /*
                 * Si el actor no existe, se crea el objeto actor y se hace un
                 * set con su nombre y se guarda, comprobando de nuevo que no esté
                 * vacío ni tenga espacios al inicio o al final de la cadena.
                 */
                if ($isset_actor == null) {
                    $actor_obj = new Actor();
                    $actor_obj->setNombre($actor);

                    if (!empty(trim($actor))) {
                        $em->persist($actor_obj);
                        $em->flush();
                    }
                }
                
                /*
                 * Se busca el actor que se acaba de añadir a la tabla actores.
                 */
                $actor = $actor_repo->findOneBy(["nombre" => $actor]);

                /*
                 * Se crea el objeto Actorpelicula para añadir el id de la película
                 * y el id del actor a la tabla actorespeliculas, asignando el actor
                 * correctamente a la película especificada.
                 */
                $Actorpelicula = new Actorpelicula();
                $Actorpelicula->setIdPelicula($pelicula);
                $Actorpelicula->setIdActor($actor);
                $em->persist($Actorpelicula);
            }
        }

        $flush = $em->flush();

        return $flush;
    }

    /*
     * Función para guardar los directores de la misma forma que los actores.
     */
    public function guardarDirectoresPelicula($directores = null, $titulo = null, $pelicula = null) {
        $em = $this->getEntityManager();

        $director_repo = $em->getRepository("KarfilmsBundle:Director");

        if ($pelicula == null) {
            $pelicula = $this->findOneBy([
                "titulo" => $titulo,
            ]);
        }

        $directores .= ",";
        $directores = explode(",", $directores);

        foreach ($directores as $director) {
            if (strlen($director) > 0) {
                $director = trim($director);
                $isset_director = $director_repo->findOneBy(["nombre" => $director]);

                if ($isset_director == null) {
                    $director_obj = new Director();
                    $director_obj->setNombre($director);

                    if (!empty(trim($director))) {
                        $em->persist($director_obj);
                        $em->flush();
                    }
                }

                $director = $director_repo->findOneBy(["nombre" => $director]);

                $Directorpelicula = new Directorpelicula();
                $Directorpelicula->setIdPelicula($pelicula);
                $Directorpelicula->setIdDirector($director);
                $em->persist($Directorpelicula);
            }
        }

        $flush = $em->flush();

        return $flush;
    }

    /*
     * Función para añadir los géneros de la misma forma que los actores y directores.
     */
    public function guardarGenerosPelicula($generos = null, $titulo = null, $pelicula = null) {
        $em = $this->getEntityManager();

        $genero_repo = $em->getRepository("KarfilmsBundle:Genero");

        if ($pelicula == null) {
            $pelicula = $this->findOneBy([
                "titulo" => $titulo,
            ]);
        }

        $generos .= ",";
        $generos = explode(",", $generos);

        foreach ($generos as $genero) {
            if (strlen($genero) > 0) {
                $genero = trim($genero);
                $isset_genero = $genero_repo->findOneBy(["nombre" => $genero]);

                if ($isset_genero == null) {
                    $genero_obj = new Genero();
                    $genero_obj->setNombre($genero);

                    if (!empty(trim($genero))) {
                        $em->persist($genero_obj);
                        $em->flush();
                    }
                }

                $genero = $genero_repo->findOneBy(["nombre" => $genero]);

                $Generopelicula = new Generopelicula();
                $Generopelicula->setIdPelicula($pelicula);
                $Generopelicula->setIdGenero($genero);
                $em->persist($Generopelicula);
            }
        }

        $flush = $em->flush();

        return $flush;
    }

}
