<?php

namespace KarfilmsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use KarfilmsBundle\Entity\Genero;
use KarfilmsBundle\Form\GeneroType;

class GeneroController extends Controller {

    private $session;

    public function __construct() {
        $this->session = new Session();
    }

    /*
     * Muestra los géneros que están en la base de datos, listándolos por orden
     * alfabético y paginados (5 actores por página).
     */

    public function mostrarGeneroAction(Request $request) {
        $em = $this->getDoctrine()->getEntityManager();

        /*
         * Creación de una query para realizar una consulta a la base de datos.
         * Selecciona todos los géneros por orden alfabético.
         */
        $dql = "SELECT g FROM KarfilmsBundle:Genero g ORDER BY g.nombre ASC";
        $query = $em->createQuery($dql);

        /*
         * Paginación.
         */
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
                $query, $request->query->getInt('page', 1), 5
        );

        return $this->render('@Karfilms/genero/mostrargenero.html.twig', [
                    "pagination" => $pagination
        ]);
    }

    /*
     * Método para mostrar las películas que tengan el género seleccionado 
     * desde la vista, recogiendo su nombre por la url y realizando
     * una consulta en la base de datos.
     */

    public function categoriaGeneroAction(Request $request, $nombre) {
        $em = $this->getDoctrine()->getEntityManager();
        $genero_repo = $em->getRepository('KarfilmsBundle:Genero');

        //Búsqueda de un género en específico por el nombre enviado desde la url
        $genero = $genero_repo->findOneBy(["nombre" => $nombre]);

        /*
         * Se recogen las películas qie tengan este género y se
         * guardan en un array.
         */
        $peliculas_obj = $genero->getGeneropelicula();

        foreach ($peliculas_obj as $pelicula) {
            $peliculas[] = $pelicula->getIdPelicula();
        }

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
                $peliculas, $request->query->getInt('page', 1), 5
        );

        /*
         * En el caso de que haya películas en la base de datos que tengan este
         * género, se envía a la vista el array. Si no hay películas,
         * simplemente se envía de vuelta el nombre del género.
         */
        if (isset($peliculas)) {
            return $this->render('@Karfilms/genero/categoriagenero.html.twig', [
                        "pagination" => $pagination,
                        'genero' => $genero
            ]);
        } else {
            return $this->render('@Karfilms/genero/categoriagenero.html.twig', [
                        'genero' => $genero
            ]);
        }
    }

    /*
     * Funcionamiento similar al método mostrarGéneroAction. Este método es para
     * la parte de administración de los géneros.
     */

    public function indiceGeneroAction(Request $request) {
        $em = $this->getDoctrine()->getEntityManager();

        $dql = "SELECT g FROM KarfilmsBundle:Genero g";
        $query = $em->createQuery($dql);

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
                $query, $request->query->getInt('page', 1), 5
        );

        return $this->render('@Karfilms/genero/indicegenero.html.twig', [
                    "pagination" => $pagination
        ]);
    }

    /*
     * Función para crear un formulario para añadir nuevos géneros a la base de
     * datos.
     */

    public function addGeneroAction(Request $request) {
        /*
         * Se crea un objeto genero nuevo y se manda con el formulario para que
         * muestre los campos de la entidad que tienen que rellenarse.
         */
        $authenticationUtils = $this->get("security.authentication_utils");
        $error = $authenticationUtils->getLastAuthenticationError();
        $genero = new Genero();
        $form = $this->createForm(GeneroType::class, $genero);

        //Se recogen los datos enviados desde el formulario.
        $form->handleRequest($request);

        //Esta parte de la función se ejecuta cuando el formulario se ha enviado y es válido.
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getEntityManager();
                $genero = new Genero();

                /*
                 * Se hace un set en la entidad Genero con el nombre introdudido
                 * en el formulario y se guarda con persist y flush.
                 */
                $genero->setNombre($form->get("nombre")->getData());

                $em->persist($genero);
                $flush = $em->flush();

                //Si la variable flush está vacía, significa que los datos se han añadido sin problema.
                if ($flush == null) {
                    $status = "Género añadido correctamente.";
                } else {
                    $status = "Error al añadir el género.";
                }
            } else {
                $status = "El género no se ha añadido porque el formulario no es válido.";
            }

            /*
             * Se envía a la vista el mensaje creado y guardado en la variable status,
             * y redirige hacia la vista de todos los géneros.
             */
            if ($status == "Género añadido correctamente.") {
                $this->session->getFlashBag()->add("status", $status);
                return $this->redirectToRoute("indice_genero");
            }
        }

        return $this->render('@Karfilms/genero/addgenero.html.twig', [
                    "form" => $form->createView(),
                    "error" => $error
        ]);
    }

    //Método para eliminar géneros, reconociendo el género en específico por el id enviado desde la url
    public function eliminarGeneroAction($id) {
        $em = $this->getDoctrine()->getEntityManager();
        $genero_repo = $em->getRepository("KarfilmsBundle:Genero");
        $genero = $genero_repo->find($id);

        $gp_repository = $em->getRepository("KarfilmsBundle:Generopelicula");
        $generos = $gp_repository->findAll();

        foreach ($generos as $gp) {
            if ($gp->getIdGenero()->getId() == $genero->getId()) {
                $em->remove($gp);
            }
        }
        $em->remove($genero);
        $em->flush();

        return $this->redirectToRoute("indice_genero");
    }

    /*
     * Función para editar los géneros de la base de datos, recogiendo el id del
     * género en cuestión enviado por la url y también los datos enviados desde
     * el formulario de edición con la variable $request.
     * Funcionamiento similar al de la función para añadir géneros.
     */

    public function editarGeneroAction($id, Request $request) {
        $authenticationUtils = $this->get("security.authentication_utils");
        $error = $authenticationUtils->getLastAuthenticationError();
        $em = $this->getDoctrine()->getEntityManager();
        $genero_repo = $em->getRepository("KarfilmsBundle:Genero");
        $genero = $genero_repo->find($id);

        $form = $this->createForm(GeneroType::class, $genero);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $genero->setNombre($form->get("nombre")->getData());

                $em->persist($genero);
                $flush = $em->flush();

                if ($flush == null) {
                    $status = "Género editado correctamente.";
                } else {
                    $status = "Error al editar el género.";
                }
            } else {
                $status = "El género no se ha editado porque el formulario no es válido.";
            }

            if ($status == "Género editado correctamente.") {
                $this->session->getFlashBag()->add("status", $status);
                return $this->redirectToRoute("indice_genero");
            }
        }

        return $this->render('@Karfilms/genero/editargenero.html.twig', [
                    "form" => $form->createView(),
                    "genero" => $genero,
                    "error" => $error
        ]);
    }

}
