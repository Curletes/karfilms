<?php

namespace KarfilmsBundle\Repository;

use KarfilmsBundle\Entity\Actor;
use KarfilmsBundle\Entity\Actorpelicula;
use KarfilmsBundle\Entity\Director;
use KarfilmsBundle\Entity\Directorpelicula;
use KarfilmsBundle\Entity\Genero;
use KarfilmsBundle\Entity\Generopelicula;

class PeliculaRepository extends \Doctrine\ORM\EntityRepository 
{
    public function guardarActoresPelicula($actores = null, $titulo = null, $pelicula = null)
    {
        $em = $this->getEntityManager();
        
        $actor_repo = $em->getRepository("KarfilmsBundle:Actor");
        $actorpelicula_repo = $em->getRepository("KarfilmsBundle:Actorpelicula");

        if($pelicula == null)
        {
            $pelicula = $this->findOneBy(["titulo" => $titulo]);
        }
        
        $actores .= ",";
        $actores = explode(",", $actores);

        foreach($actores as $actor)
        {
            $actor = trim($actor);
            if(strlen($actor) > 0)
            {
                $isset_actor = $actor_repo->findOneBy(["nombre" => $actor]);

                if($isset_actor == null)
                {
                    $actor_obj = new Actor();
                    $actor_obj->setNombre($actor);
                    
                    if(!empty($actor))
                    {
                        $em->persist($actor_obj);
                        $em->flush();
                    }
                }
                $actor = $actor_repo->findOneBy(["nombre" => $actor]);

                $id = $actor->getId();
                $isset_actorpelicula = $actorpelicula_repo->findOneBy(["idActor" => $id]);

                if($isset_actorpelicula == null)
                {
                    $Actorpelicula = new Actorpelicula();
                
                    $Actorpelicula->setIdPelicula($pelicula);
                    $Actorpelicula->setIdActor($actor);

                    $em->persist($Actorpelicula);
                    
                    $flush = $em->flush();
                }
            }
        }
        
        if(isset($flush))
        {
            return $flush;
        }
    }
    
    public function guardarDirectoresPelicula($directores = null, $titulo = null, $pelicula = null)
    {
        $em = $this->getEntityManager();
        
        $director_repo = $em->getRepository("KarfilmsBundle:Director");
        $directorpelicula_repo = $em->getRepository("KarfilmsBundle:Directorpelicula");
        
        if($pelicula == null)
        {
            $pelicula = $this->findOneBy(["titulo" => $titulo]);
        }
        
        $directores .= ",";
        $directores = explode(",", $directores);

        foreach($directores as $director)
        {
            $director = trim($director);
            if(strlen($director) > 0)
            {
                $isset_director = $director_repo->findOneBy(["nombre" => $director]);

                if($isset_director == null)
                {
                    $director_obj = new Director();
                    $director_obj->setNombre($director);
                    
                    if(!empty($director))
                    {
                        $em->persist($director_obj);
                        $em->flush();
                    }
                }
                $director = $director_repo->findOneBy(["nombre" => $director]);
                $id = $director->getId();
                $isset_directorpelicula = $directorpelicula_repo->findOneBy(["idDirector" => $id]);

                if($isset_directorpelicula == null)
                {
                    $Directorpelicula = new Directorpelicula();
                
                    $Directorpelicula->setIdPelicula($pelicula);
                    $Directorpelicula->setIdDirector($director);

                    $em->persist($Directorpelicula);
                    
                    $flush = $em->flush();
                }
            }
        }
        
        if(isset($flush))
        {
            return $flush;
        }
    }
    
    public function guardarGenerosPelicula($generos = null, $titulo = null, $pelicula = null)
    {
        $em = $this->getEntityManager();
        
        $genero_repo = $em->getRepository("KarfilmsBundle:Genero");
        $generopelicula_repo = $em->getRepository("KarfilmsBundle:Generopelicula");
        
        if($pelicula == null)
        {
            $pelicula = $this->findOneBy(["titulo" => $titulo]);
        }
        
        $generos .= ",";
        $generos = explode(",", $generos);

        foreach($generos as $genero)
        {
            $genero = trim($genero);
            if(strlen($genero) > 0)
            {
                $isset_genero = $genero_repo->findOneBy(["nombre" => $genero]);

                if($isset_genero == null)
                {
                    $genero_obj = new Genero();
                    $genero_obj->setNombre($genero);
                    
                    if(!empty($genero))
                    {
                        $em->persist($genero_obj);
                        $em->flush();
                    }
                }
                $genero = $genero_repo->findOneBy(["nombre" => $genero]);
                $id = $genero->getId();
                $isset_generopelicula = $generopelicula_repo->findOneBy(["idGenero" => $id]);

                if($isset_generopelicula == null)
                {
                    $Generopelicula = new Generopelicula();
                
                    $Generopelicula->setIdPelicula($pelicula);
                    $Generopelicula->setIdGenero($genero);

                    $em->persist($Generopelicula);
                    
                    $flush = $em->flush();
                }
            }
        }
        
        if(isset($flush))
        {
            return $flush;
        }
    }
}
