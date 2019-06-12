<?php

namespace KarfilmsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use KarfilmsBundle\Entity\Actor;
use KarfilmsBundle\Form\ActorType;

class ActorController extends Controller {

    private $session;

    public function __construct() {
        $this->session = new Session();
    }

    /*
     * Muestra los actores que están en la base de datos, listándolos por orden
     * alfabético y paginados (5 actores por página).
     */
    public function mostrarActorAction(Request $request) {
        $em = $this->getDoctrine()->getEntityManager();        
        /*
         * Creación de una query para realizar una consulta a la base de datos.
         * Selecciona todos los actores por orden alfabético.
         */
        $dql = "SELECT a FROM KarfilmsBundle:Actor a ORDER BY a.nombre ASC";
        $query = $em->createQuery($dql);
 
        /*
         * Paginación.
         */
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
                $query,
                $request->query->getInt('page', 1),
                5
        );

        return $this->render('@Karfilms/actor/mostraractor.html.twig', [
                    "pagination" => $pagination
        ]);
    }

    /*
     * Método para mostrar las películas en las que ha participado el actor
     * seleccionado desde la vista, recogiendo su nombre por la url y realizando
     * una consulta en la base de datos.
     */
    public function categoriaActorAction(Request $request, $nombre) {
        $em = $this->getDoctrine()->getEntityManager();
        $actor_repo = $em->getRepository('KarfilmsBundle:Actor');
        
        //Búsqueda de un actor en específico por el nombre enviado desde la url
        $actor = $actor_repo->findOneBy(["nombre" => $nombre]);

        /*
         * Se recogen las películas en las que ha participado el actor y se
         * guardan en un array.
         */
        $peliculas_obj = $actor->getActorpelicula();

        foreach ($peliculas_obj as $pelicula) {
            $peliculas[] = $pelicula->getIdPelicula();
        }
        
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
                $peliculas,
                $request->query->getInt('page', 1),
                5
        );
        
        /*
         * En el caso de que haya películas en la base de datos en las que ha
         * participado dicho actor, se envía a la vista el array. Si no hay películas,
         * simplemente se envía de vuelta el nombre del actor.
         */
        if (isset($peliculas)) {
            return $this->render('@Karfilms/actor/categoriaactor.html.twig', [
                        "pagination" => $pagination,
                        'actor' => $actor
            ]);
        } else {
            return $this->render('@Karfilms/actor/categoriaactor.html.twig', [
                        'actor' => $actor
            ]);
        }
    }

    /*
     * Funcionamiento similar al método mostrarActorAction. Este método es para
     * la parte de administración de los actores.
     */
    public function indiceActorAction(Request $request) {
        $em = $this->getDoctrine()->getEntityManager();
        
        $dql = "SELECT a FROM KarfilmsBundle:Actor a ORDER BY a.nombre ASC";
        $query = $em->createQuery($dql);
 
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
                $query,
                $request->query->getInt('page', 1),
                5
        );

        return $this->render('@Karfilms/actor/indiceactor.html.twig', [
                    "pagination" => $pagination
        ]);
    }

    /*
     * Función para crear un formulario para añadir nuevos actores a la base de
     * datos.
     */
    public function addActorAction(Request $request) {
        /*
         * Se crea un objeto actor nuevo y se manda con el formulario para que
         * muestre los campos de la entidad que tienen que rellenarse.
         */
        $actor = new Actor();
        $form = $this->createForm(ActorType::class, $actor);

        //Se recogen los datos enviados desde el formulario.
        $form->handleRequest($request);

        //Esta parte de la función se ejecuta cuando el formulario se ha enviado y es válido.
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getEntityManager();
                $actor = new Actor();
                
                /*
                 * Se hace un set en la entidad Actor con el nombre introdudido
                 * en el formulario y se guarda con persist y flush.
                 */
                $actor->setNombre($form->get("nombre")->getData());

                $em->persist($actor);
                $flush = $em->flush();
                
                //Si la variable flush está vacía, significa que los datos se han añadido sin problema.
                if ($flush == null) {
                    $status = "Actor añadido correctamente.";
                } else {
                    $status = "Error al añadir el actor.";
                }
            } else {
                $status = "El actor no se ha añadido porque el formulario no es válido.";
            }
            
            /*
             * Se envía a la vista el mensaje creado y guardado en la variable status,
             * y redirige hacia la vista de todos los actores.
             */
            $this->session->getFlashBag()->add("status", $status);
            return $this->redirectToRoute("indice_actor");
        }

        return $this->render('@Karfilms/actor/addactor.html.twig', [
                    "form" => $form->createView()
        ]);
    }
    
    //Método para eliminar actores, reconociendo el actor en específico por el id enviado desde la url
    public function eliminarActorAction($id) {
        $em = $this->getDoctrine()->getEntityManager();
        $actor_repo = $em->getRepository("KarfilmsBundle:Actor");
        $actor = $actor_repo->find($id);

        //Si el actor no está en ninguna película de la base de datos, se borra
        if (count($actor->getActorpelicula()) == 0) {
            $em->remove($actor);
            $em->flush();
        }

        return $this->redirectToRoute("indice_actor");
    }

    /*
     * Función para editar los actores de la base de datos, recogiendo el id del
     * actor en cuestión enviado por la url y también los datos enviados desde
     * el formulario de edición con la variable $request.
     * Funcionamiento similar al de la función para añadir actores.
     */
    public function editarActorAction($id, Request $request) {
        $em = $this->getDoctrine()->getEntityManager();
        $actor_repo = $em->getRepository("KarfilmsBundle:Actor");
        $actor = $actor_repo->find($id);

        $form = $this->createForm(ActorType::class, $actor);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $actor->setNombre($form->get("nombre")->getData());

                $em->persist($actor);
                $flush = $em->flush();

                if ($flush == null) {
                    $status = "Actor editado correctamente.";
                } else {
                    $status = "Error al editar el actor.";
                }
            } else {
                $status = "El actor no se ha editado porque el formulario no es válido.";
            }

            $this->session->getFlashBag()->add("status", $status);
            return $this->redirectToRoute("indice_actor");
        }

        return $this->render('@Karfilms/actor/editaractor.html.twig', [
                    "form" => $form->createView()
        ]);
    }

}
