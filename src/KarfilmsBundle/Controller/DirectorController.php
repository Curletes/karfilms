<?php

namespace KarfilmsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use KarfilmsBundle\Entity\Director;
use KarfilmsBundle\Form\DirectorType;

class DirectorController extends Controller {

    private $session;

    public function __construct() {
        $this->session = new Session();
    }

    /*
     * Muestra los directores que están en la base de datos, listándolos por orden
     * alfabético y paginados (5 directores por página).
     */

    public function mostrarDirectorAction(Request $request) {
        $em = $this->getDoctrine()->getEntityManager();
        /*
         * Creación de una query para realizar una consulta a la base de datos.
         * Selecciona todos los directores por orden alfabético.
         */
        $dql = "SELECT d FROM KarfilmsBundle:Director d ORDER BY d.nombre ASC";
        $query = $em->createQuery($dql);

        //Paginación
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
                $query, $request->query->getInt('page', 1), 5
        );

        return $this->render('@Karfilms/director/mostrardirector.html.twig', [
                    "pagination" => $pagination
        ]);
    }

    /*
     * Método para mostrar las películas en las que ha participado el director
     * seleccionado desde la vista, recogiendo su nombre por la url y realizando
     * una consulta en la base de datos.
     */

    public function categoriaDirectorAction(Request $request, $nombre) {
        $em = $this->getDoctrine()->getEntityManager();
        $director_repo = $em->getRepository('KarfilmsBundle:Director');

        //Búsqueda de un director en específico por el nombre enviado desde la url
        $director = $director_repo->findOneBy(["nombre" => $nombre]);

        /*
         * Se recogen las películas en las que ha participado el director y se
         * guardan en un array.
         */
        $peliculas_obj = $director->getDirectorpelicula();

        foreach ($peliculas_obj as $pelicula) {
            $peliculas[] = $pelicula->getIdPelicula();
        }

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
                $peliculas, $request->query->getInt('page', 1), 5
        );

        /*
         * En el caso de que haya películas en la base de datos en las que ha
         * participado dicho director, se envía a la vista el array. Si no hay películas,
         * simplemente se envía de vuelta el nombre del director.
         */
        if (isset($peliculas)) {
            return $this->render('@Karfilms/director/categoriadirector.html.twig', [
                        "pagination" => $pagination,
                        'director' => $director
            ]);
        } else {
            return $this->render('@Karfilms/director/categoriadirector.html.twig', [
                        'director' => $director
            ]);
        }
    }

    /*
     * Funcionamiento similar al método mostrarDirectorAction. Este método es para
     * la parte de administración de los directores.
     */

    public function indiceDirectorAction(Request $request) {
        $em = $this->getDoctrine()->getEntityManager();

        $dql = "SELECT d FROM KarfilmsBundle:Director d ORDER BY d.nombre ASC";
        $query = $em->createQuery($dql);

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
                $query, $request->query->getInt('page', 1), 5
        );

        return $this->render('@Karfilms/director/indicedirector.html.twig', [
                    "pagination" => $pagination
        ]);
    }

    /*
     * Función para crear un formulario para añadir nuevos directores a la base de
     * datos.
     */

    public function addDirectorAction(Request $request) {
        /*
         * Se crea un objeto director nuevo y se manda con el formulario para que
         * muestre los campos de la entidad que tienen que rellenarse.
         */
        $director = new Director();
        $form = $this->createForm(DirectorType::class, $director);

        //Se recogen los datos enviados desde el formulario.
        $form->handleRequest($request);

        //Esta parte de la función se ejecuta cuando el formulario se ha enviado y es válido.
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getEntityManager();
                $director = new Director();

                /*
                 * Se hace un set en la entidad Director con el nombre introdudido
                 * en el formulario y se guarda con persist y flush.
                 */
                $director->setNombre($form->get("nombre")->getData());

                $em->persist($director);
                $flush = $em->flush();

                //Si la variable flush está vacía, significa que los datos se han añadido sin problema.
                if ($flush == null) {
                    $status = "Director añadido correctamente.";
                } else {
                    $status = "Error al añadir el director.";
                }
            } else {
                $status = "El director no se ha añadido porque el formulario no es válido.";
            }

            /*
             * Se envía a la vista el mensaje creado y guardado en la variable status,
             * y redirige hacia la vista de todos los directores.
             */
            $this->session->getFlashBag()->add("status", $status);
            return $this->redirectToRoute("indice_director");
        }

        return $this->render('@Karfilms/director/adddirector.html.twig', [
                    "form" => $form->createView()
        ]);
    }

    //Método para eliminar directores, reconociendo el director en específico por el id enviado desde la url
    public function eliminarDirectorAction($id) {
        $em = $this->getDoctrine()->getEntityManager();
        $director_repo = $em->getRepository("KarfilmsBundle:Director");
        $director = $director_repo->find($id);

        $dp_repository = $em->getRepository("KarfilmsBundle:Directorpelicula");
        $directores = $dp_repository->findAll();

        foreach ($directores as $dp) {
            if ($dp->getIdDirector()->getId() == $director->getId()) {
                $em->remove($dp);
            }
        }

        $em->remove($director);
        $em->flush();

        return $this->redirectToRoute("indice_director");
    }

    /*
     * Función para editar los directores de la base de datos, recogiendo el id del
     * director en cuestión enviado por la url y también los datos enviados desde
     * el formulario de edición con la variable $request.
     * Funcionamiento similar al de la función para añadir directores.
     */

    public function editarDirectorAction($id, Request $request) {
        $em = $this->getDoctrine()->getEntityManager();
        $director_repo = $em->getRepository("KarfilmsBundle:Director");
        $director = $director_repo->find($id);

        $form = $this->createForm(DirectorType::class, $director);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $director->setNombre($form->get("nombre")->getData());

                $em->persist($director);
                $flush = $em->flush();

                if ($flush == null) {
                    $status = "Director editado correctamente.";
                } else {
                    $status = "Error al editar el director.";
                }
            } else {
                $status = "El director no se ha editado porque el formulario no es válido.";
            }

            $this->session->getFlashBag()->add("status", $status);
            return $this->redirectToRoute("indice_director");
        }

        return $this->render('@Karfilms/director/editardirector.html.twig', [
                    "form" => $form->createView(),
                    "director" => $director
        ]);
    }

}
