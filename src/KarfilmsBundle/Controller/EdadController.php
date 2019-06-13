<?php

namespace KarfilmsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use KarfilmsBundle\Entity\Edad;
use KarfilmsBundle\Form\EdadType;

class EdadController extends Controller {

    private $session;

    public function __construct() {
        $this->session = new Session();
    }

    /*
     * Muestra las clasificaciones por edades que están en la base de datos.
     */
    public function mostrarEdadAction() {
        $em = $this->getDoctrine()->getEntityManager();
        $edad_repo = $em->getRepository("KarfilmsBundle:Edad");
        $edades = $edad_repo->findAll();

        return $this->render('@Karfilms/edad/mostraredad.html.twig', [
                    "edades" => $edades
        ]);
    }

    /*
     * Método para mostrar las películas que estén clasificadas según la edad
     * seleccionada desde la vista, recogiendo su nombre por la url y realizando
     * una consulta en la base de datos.
     */
    public function categoriaEdadAction(Request $request, $clasificacion) {
        $em = $this->getDoctrine()->getEntityManager();
        $edad_repo = $em->getRepository('KarfilmsBundle:Edad');

        //Búsqueda de la clasificación en específico por el nombre enviado desde la url
        $edad = $edad_repo->findOneBy(["clasificacion" => $clasificacion]);
        $idEdad = $edad->getId();

        /*
         * Se recogen las películas que tengan la clasificación especificada y se
         * guardan en un array.
         */
        $pelicula_repo = $em->getRepository("KarfilmsBundle:Pelicula");
        $peliculas = $pelicula_repo->findBy(["idEdad" => $idEdad]);

        //Paginación. 5 películas por página.
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
                $peliculas, $request->query->getInt('page', 1), 5
        );

        /*
         * En el caso de que haya películas en la base de datos con esta clasificación, 
         * se envía a la vista el array. Si no hay películas,
         * simplemente se envía de vuelta el nombre de la clasificación.
         */
        if (isset($peliculas)) {
            return $this->render('@Karfilms/edad/categoriaedad.html.twig', [
                        'edad' => $edad,
                        "pagination" => $pagination
            ]);
        } else {
            return $this->render('@Karfilms/edad/categoriaedad.html.twig', [
                        'edad' => $edad
            ]);
        }
    }

    /*
     * Funcionamiento similar al método mostrarEdadAction. Este método es para
     * la parte de administración de las edades.
     */
    public function indiceEdadAction() {
        $em = $this->getDoctrine()->getEntityManager();
        $edad_repo = $em->getRepository("KarfilmsBundle:Edad");
        $edades = $edad_repo->findAll();

        $pelicula_repo = $em->getRepository("KarfilmsBundle:Pelicula");
        $peliculas = $pelicula_repo->findAll();

        return $this->render('@Karfilms/edad/indiceedad.html.twig', [
                    "edades" => $edades,
                    "peliculas" => $peliculas
        ]);
    }

    /*
     * Función para crear un formulario para añadir nuevas edades a la base de
     * datos.
     */
    public function addEdadAction(Request $request) {
        /*
         * Se crea un objeto edad nuevo y se manda con el formulario para que
         * muestre los campos de la entidad que tienen que rellenarse.
         */
        $edad = new Edad();
        $form = $this->createForm(EdadType::class, $edad);

        //Se recogen los datos enviados desde el formulario.
        $form->handleRequest($request);

        //Esta parte de la función se ejecuta cuando el formulario se ha enviado y es válido.
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getEntityManager();
                $edad = new Edad();
                
                /*
                 * Se hace un set en la entidad Edad con el nombre de la clasificación
                 * introducida en el formulario y se guarda con persist y flush.
                 */
                $edad->setClasificacion($form->get("clasificacion")->getData());

                $em->persist($edad);
                $flush = $em->flush();

                //Si la variable flush está vacía, significa que los datos se han añadido sin problema.
                if ($flush == null) {
                    $status = "Edad añadida correctamente.";
                } else {
                    $status = "Error al añadir la edad.";
                }
            } else {
                $status = "El edad no se ha añadida porque el formulario no es válido.";
            }

            /*
             * Se envía a la vista el mensaje creado y guardado en la variable status,
             * y redirige hacia la vista de todas las clasificaciones por edad.
             */
            $this->session->getFlashBag()->add("status", $status);
            return $this->redirectToRoute("indice_edad");
        }

        return $this->render('@Karfilms/edad/addedad.html.twig', [
                    "form" => $form->createView()
        ]);
    }

    //Método para eliminar edades, reconociendo la edad en específico por el id enviado desde la url
    public function eliminarEdadAction($id) {
        $em = $this->getDoctrine()->getEntityManager();
        $edad_repo = $em->getRepository("KarfilmsBundle:Edad");
        $edad = $edad_repo->find($id);

        /*
         * Se recogen las películas que estén registradas en la base de datos.
         */
        $pelicula_repo = $em->getRepository("KarfilmsBundle:Pelicula");
        $peliculas = $pelicula_repo->findAll();

        /*
         * Si esta clasificación no tiene ninguna película, puede ser eliminada.
         */
        foreach ($peliculas as $pelicula) {
            if ($pelicula->getIdEdad()->getId() != $edad->getId()) {
                $em->remove($edad);
                $em->flush();
            }
        }

        return $this->redirectToRoute("indice_edad");
    }

    /*
     * Función para editar las edades de la base de datos, recogiendo el id de
     * la clasificación en cuestión enviado por la url y también los datos enviados desde
     * el formulario de edición con la variable $request.
     * Funcionamiento similar al de la función para añadir edades.
     */
    public function editarEdadAction($id, Request $request) {
        $em = $this->getDoctrine()->getEntityManager();
        $edad_repo = $em->getRepository("KarfilmsBundle:Edad");
        $edad = $edad_repo->find($id);

        $form = $this->createForm(EdadType::class, $edad);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $edad->setClasificacion($form->get("clasificacion")->getData());

                $em->persist($edad);
                $flush = $em->flush();

                if ($flush == null) {
                    $status = "Edad editada correctamente.";
                } else {
                    $status = "Error al editar la edad.";
                }
            } else {
                $status = "El edad no se ha editada porque el formulario no es válido.";
            }

            $this->session->getFlashBag()->add("status", $status);
            return $this->redirectToRoute("indice_edad");
        }

        return $this->render('@Karfilms/edad/editaredad.html.twig', [
                    "form" => $form->createView(),
                    "edad" => $edad
        ]);
    }

}
