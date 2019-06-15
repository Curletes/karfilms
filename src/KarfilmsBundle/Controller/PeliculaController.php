<?php

namespace KarfilmsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use KarfilmsBundle\Entity\Pelicula;
use KarfilmsBundle\Form\PeliculaType;
use KarfilmsBundle\Form\EditarPeliculaType;
use Symfony\Component\HttpFoundation\JsonResponse;

class PeliculaController extends Controller {

    private $session;

    public function __construct() {
        $this->session = new Session();
    }

    /*
     * Muestra las películas que están en la base de datos, listándolas por orden
     * alfabético y paginadas (5 películas por página).
     */

    public function mostrarCarteleraAction(Request $request) {
        $em = $this->getDoctrine()->getEntityManager();
        /*
         * Creación de una query para realizar una consulta a la base de datos.
         * Selecciona todas las películas por orden alfabético.
         */
        $dql = "SELECT p FROM KarfilmsBundle:Pelicula p ORDER BY p.titulo ASC";
        $query = $em->createQuery($dql);

        /*
         * Paginación.
         */
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
                $query, $request->query->getInt('page', 1), 5
        );

        return $this->render('@Karfilms/pelicula/mostrarcartelera.html.twig', [
                    "pagination" => $pagination
        ]);
    }

    /*
     * Función para realizar una consulta llamada a través de Ajax.
     * Se recogen las películas cuyo título coincida con lo que el usuario
     * haya escrito en el buscador de la barra de navegación.
     */

    public function pelicula($pelicula) {
        $em = $this->getDoctrine()->getEntityManager();

        return $em->createQuery("SELECT p.id, p.titulo "
                                . "FROM KarfilmsBundle:Pelicula p "
                                . "WHERE p.titulo LIKE :pelicula "
                                . "ORDER BY p.titulo ASC")
                        ->setParameter("pelicula", "%" . $pelicula . "%")
                        ->getResult();
    }

    /*
     * Función de Ajax. Recoge lo que el usuario ha escrito en el buscador y lo
     * manda a la función pelicula especificada anteriormente.
     * Guarda el resultado en formato json y lo devuelve a la vista para mostrar
     * los resultados.
     */

    public function peliculaAjaxAction(Request $request) {
        $pelicula = $request->request->get('pelicula');
        $peliculas = $this->pelicula($pelicula);
        $response = new JsonResponse(['peliculas' => $peliculas]);

        return $response;
    }

    /*
     * Función para realizar una consulta llamada a través de Ajax.
     * Se recogen las películas cuya sesión coincida con lo que el usuario
     * haya seleccionado en el formulario donde se muestran los días en los que
     * la película en específico tiene alguna sesión.
     */

    public function sesionesPeliculaCartelera($diames, $id) {
        $em = $this->getDoctrine()->getEntityManager();

        /*
         * Se ha guardado el día y el mes enviados por el formulario en la variable
         * $diames. Después se han separado el día y el mes en dos variables distintas
         * para poder introducirlos correctamente en la consulta a realizar.
         */
        $dias = explode("-", $diames);
        $dia = $dias[1] . "-" . $dias[0];

        /*
         * Selecciona las películas que tengan una sesión que coincida con el día
         * y el mes especificado.
         */
        return $em->createQuery("SELECT s.horarios "
                                . "FROM KarfilmsBundle:Sesion s "
                                . "WHERE s.idPelicula = :id "
                                . "AND s.horarios LIKE :dia")
                        ->setParameter("id", $id)->setParameter("dia", "%" . $dia . "%")
                        ->getResult();
    }

    /*
     * Función de Ajax. Recoge lo que el usuario ha seleccionado en el formulario y lo
     * manda a la función pelicula especificada anteriormente.
     * Guarda el resultado en formato json y lo devuelve a la vista para mostrar
     * los resultados.
     */

    public function sesionesPeliculaCarteleraAjaxAction(Request $request) {
        $diames = $request->request->get('diames');
        $id = $request->request->get('id');
        $sesiones = $this->sesionesPeliculaCartelera($diames, $id);

        $response = new JsonResponse(['sesiones' => $sesiones]);

        return $response;
    }

    /*
     * Funcionamiento similar al método mostrarCarteleraAction. Este método es para
     * la parte de administración de las películas.
     */

    public function indicePeliculaAction(Request $request) {
        $em = $this->getDoctrine()->getEntityManager();

        $dql = "SELECT p FROM KarfilmsBundle:Pelicula p";
        $query = $em->createQuery($dql);

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
                $query, $request->query->getInt('page', 1), 5
        );

        return $this->render('@Karfilms/pelicula/indicepelicula.html.twig', [
                    "pagination" => $pagination
        ]);
    }

    /*
     * Función para mostrar los datos de la película que ha seleccionado el usuario.
     * Se ha enviado el id de dicha película a través de la url y se recoge en la función
     * para identificarla correctamente.
     * Esta función es para la página de administración.
     */

    public function detallesPeliculaAction($id) {
        /*
         * Creación de arrays para guardar los directores, actores y géneros que
         * tiene dicha película.
         */
        $directores = [];
        $actores = [];
        $generos = [];

        /*
         * Se recogen todas las películas que hay en la base de datos, creando un
         * repositorio, y se guarda la película especificada a través del id en
         * la variable $pelicula.
         */
        $em = $this->getDoctrine()->getEntityManager();
        $pelicula_repo = $em->getRepository("KarfilmsBundle:Pelicula");
        $pelicula = $pelicula_repo->find($id);

        /*
         * Se guardan todos los directores, actores y géneros que aparezcan en las 
         * tablaw directorespeliculas, actorespeliculas y generospeliculas
         * para la película especificada.
         */
        $Directorpelicula = $pelicula->getDirectorpelicula();

        foreach ($Directorpelicula as $director) {
            $directores[] = $director->getIdDirector()->getNombre();
        }

        $Actorpelicula = $pelicula->getActorpelicula();

        foreach ($Actorpelicula as $actor) {
            $actores[] = $actor->getIdActor()->getNombre();
        }

        $Generopelicula = $pelicula->getGeneropelicula();

        foreach ($Generopelicula as $genero) {
            $generos[] = $genero->getIdGenero()->getNombre();
        }

        /*
         * Se devuelven la película, los directores, los actores y los géneros
         * a la vista.
         */
        return $this->render('@Karfilms/pelicula/detallespelicula.html.twig', [
                    "pelicula" => $pelicula,
                    "directores" => $directores,
                    "actores" => $actores,
                    "generos" => $generos
        ]);
    }

    /*
     * Funciona igual que la función anterior, pero esta es para la vista de
     * los usuarios normales.
     */

    public function mostrarPeliculaAction($id) {
        $directores = [];
        $actores = [];
        $generos = [];
        $estadocartelera = "active";

        $em = $this->getDoctrine()->getEntityManager();
        $pelicula_repo = $em->getRepository("KarfilmsBundle:Pelicula");
        $pelicula = $pelicula_repo->find($id);

        $Directorpelicula = $pelicula->getDirectorpelicula();

        foreach ($Directorpelicula as $director) {
            $directores[] = $director->getIdDirector()->getNombre();
        }

        $Actorpelicula = $pelicula->getActorpelicula();

        foreach ($Actorpelicula as $actor) {
            $actores[] = $actor->getIdActor()->getNombre();
        }

        $Generopelicula = $pelicula->getGeneropelicula();

        foreach ($Generopelicula as $genero) {
            $generos[] = $genero->getIdGenero()->getNombre();
        }

        return $this->render('@Karfilms/pelicula/detallespelicula.html.twig', [
                    "pelicula" => $pelicula,
                    "directores" => $directores,
                    "actores" => $actores,
                    "generos" => $generos,
                    "active" => $estadocartelera
        ]);
    }

    /*
     * Función para crear un formulario para añadir nuevas películas a la base de
     * datos.
     */

    public function addPeliculaAction(Request $request) {
        /*
         * Se crea un objeto pelicula nuevo y se manda con el formulario para que
         * muestre los campos de la entidad que tienen que rellenarse.
         */
        $authenticationUtils = $this->get("security.authentication_utils");
        $error = $authenticationUtils->getLastAuthenticationError();
        $pelicula = new Pelicula();
        $form = $this->createForm(PeliculaType::class, $pelicula);

        //Se recogen los datos enviados desde el formulario.
        $form->handleRequest($request);

        //Esta parte de la función se ejecuta cuando el formulario se ha enviado y es válido.
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getEntityManager();
                $pelicula_repo = $em->getRepository("KarfilmsBundle:Pelicula");

                /*
                 * Se hace un set en la entidad Pelicula con el título y sinopsis 
                 * introdudidos en el formulario.
                 */
                $pelicula->setTitulo($form->get("titulo")->getData());
                $pelicula->setSinopsis($form->get("sinopsis")->getData());

                /*
                 * Se recoge la imagen que se ha enviado y se guarda en la variable
                 * $ficheroCartel.
                 */
                $ficheroCartel = $form["cartel"]->getData();

                if ($ficheroCartel != null) {
                    /*
                     * Se guarda la extensión del fichero en una variable para 
                     * comprobar que los datos son correctos.
                     */
                    $ext = $ficheroCartel->guessExtension();

                    /*
                     * Solamente si la extensión es .jpg, .png o .jpeg la imagen
                     * se guardará en la carpeta correspondiente.
                     */
                    if ($ext == "jpg" || $ext == "png" || $ext == "jpeg") {
                        /*
                         * Se crea un nombre para el fichero que consista en la
                         * hora actual. De esta forma nunca habrá dos imágenes con
                         * el mismo nombre en la carpeta.
                         */
                        $nombre_fichero = time() . "." . $ext;
                        /*
                         * Se mueve la imagen a la carpeta especificada.
                         */
                        $ficheroCartel->move("imagenes/carteles", $nombre_fichero);

                        /*
                         * Se hace un set para guardar en la base de datos el nombre del fichero.
                         */
                        $pelicula->setCartel($nombre_fichero);
                    } else {
                        /*
                         * Si la extensión del fichero no es correcta, se mandará
                         * un mensaje de error al usuario y lo devolverá a la página
                         * de administración de películas.
                         */
                        $status2 = "Sólo se permiten las extensiones .jpg, .jpeg y .png.";
                        $this->session->getFlashBag()->add("status", $status2);
                        return $this->redirectToRoute("indice_pelicula");
                    }
                }

                /*
                 * La siguiente sección funciona de la misma forma que la anterior, solo que 
                 * para los vídeos en lugar de las imágenes.
                 */
                $ficheroTrailer = $form["trailer"]->getData();

                if ($ficheroTrailer != null) {
                    $ext = $ficheroTrailer->guessExtension();

                    if ($ext == "mp4" || $ext == "avi" || $ext == "wmv" || $ext == "mov") {
                        $nombre_fichero = time() . "." . $ext;
                        $ficheroTrailer->move("imagenes/trailers", $nombre_fichero);

                        $pelicula->setTrailer($nombre_fichero);
                    } else {
                        $status2 = "Sólo se permiten las extensiones .mp4, .avi, .wmv y .mov.";
                        $this->session->getFlashBag()->add("status", $status2);
                        return $this->redirectToRoute("indice_pelicula");
                    }
                }

                /*
                 * Set de la duración de la película y la clasificación por edades
                 * que se ha seleccionado a través del formulario.
                 */
                $pelicula->setDuracion($form->get("duracion")->getData());
                $pelicula->setIdEdad($form->get("idEdad")->getData());

                /*
                 * Se guardan los cambios realizados.
                 */
                $em->persist($pelicula);
                $flush = $em->flush();

                /*
                 * Funciones para llamar al repositorio que añade
                 * los actores, directores y géneros escritos
                 * en el formulario.
                 * (PeliculaRepository.php)
                 */
                $pelicula_repo->guardarActoresPelicula(
                        $form->get("actores")->getData(), $form->get("titulo")->getData()
                );

                $pelicula_repo->guardarDirectoresPelicula(
                        $form->get("directores")->getData(), $form->get("titulo")->getData()
                );

                $pelicula_repo->guardarGenerosPelicula(
                        $form->get("generos")->getData(), $form->get("titulo")->getData()
                );

                if ($flush == null) {
                    $status = "Película añadida correctamente.";
                } else {
                    $status = "Error al añadir la película.";
                }
            } else {
                $status = "La pelicula no se ha añadido porque el formulario no es válido.";
            }

            /*
             * Se envía a la vista el mensaje creado y guardado en la variable status,
             * y redirige hacia la vista de todas las películas.
             */
            if ("Película añadida correctamente.") {
                $this->session->getFlashBag()->add("status", $status);
                return $this->redirectToRoute("indice_pelicula");
            }
        }

        return $this->render('@Karfilms/pelicula/addpelicula.html.twig', [
                    "form" => $form->createView(),
                    "error" => $error
        ]);
    }

    //Método para eliminar películas, reconociendo la película en específico por el id enviado desde la url
    public function eliminarPeliculaAction($id) {
        $em = $this->getDoctrine()->getEntityManager();
        $pelicula_repo = $em->getRepository("KarfilmsBundle:Pelicula");
        $pelicula = $pelicula_repo->find($id);

        /*
         * Creación de repositorios para las sesiones, los directores, actores y géneros que están
         * asignados a esta película.
         */
        $actorpelicula_repo = $em->getRepository("KarfilmsBundle:Actorpelicula");
        $directorpelicula_repo = $em->getRepository("KarfilmsBundle:Directorpelicula");
        $generopelicula_repo = $em->getRepository("KarfilmsBundle:Generopelicula");
        $sesion_repo = $em->getRepository("KarfilmsBundle:Sesion");

        /*
         * Búsqueda de las sesiones, los actores, directores y géneros de las tablas
         * sesiones, actorespeliculas, directorespeliculas y generospeliculas 
         * según la película especificada, para luego borrarlos.
         */
        $sesiones = $sesion_repo->findAll();

        foreach ($sesiones as $sesion) {
            if ($sesion->getIdPelicula()->getId() == $pelicula->getId()) {
                $em->remove($sesion);
                $em->flush();
            }
        }

        $actorespelicula = $actorpelicula_repo->findBy(["idPelicula" => $pelicula]);

        foreach ($actorespelicula as $ap) {
            $em->remove($ap);
            $em->flush();
        }

        $directorespelicula = $directorpelicula_repo->findBy(["idPelicula" => $pelicula]);

        foreach ($directorespelicula as $dp) {
            $em->remove($dp);
            $em->flush();
        }

        $generospelicula = $generopelicula_repo->findBy(["idPelicula" => $pelicula]);

        foreach ($generospelicula as $gp) {
            $em->remove($gp);
            $em->flush();
        }

        if (count($pelicula->getSesiones()) == 0) {
            $em->remove($pelicula);
            $em->flush();
        }

        return $this->redirectToRoute("indice_pelicula");
    }

    /*
     * Función para editar las películas de la base de datos, recogiendo el id del
     * actor en cuestión enviado por la url y también los datos enviados desde
     * el formulario de edición con la variable $request.
     * Funcionamiento similar al de la función para añadir películas.
     */

    public function editarPeliculaAction($id, Request $request) {
        $authenticationUtils = $this->get("security.authentication_utils");
        $error = $authenticationUtils->getLastAuthenticationError();
        $em = $this->getDoctrine()->getEntityManager();
        $pelicula_repo = $em->getRepository("KarfilmsBundle:Pelicula");
        $pelicula = $pelicula_repo->find($id);

        $cartel = $pelicula->getCartel();
        $trailer = $pelicula->getTrailer();

        $actores = "";
        $directores = "";
        $generos = "";

        foreach ($pelicula->getActorpelicula() as $Actorpelicula) {
            $actores .= $Actorpelicula->getIdActor()->getNombre() . ", ";
        }

        foreach ($pelicula->getDirectorpelicula() as $Directorpelicula) {
            $directores .= $Directorpelicula->getIdDirector()->getNombre() . ", ";
        }

        foreach ($pelicula->getGeneropelicula() as $Generopelicula) {
            $generos .= $Generopelicula->getIdGenero()->getNombre() . ", ";
        }

        $form = $this->createForm(EditarPeliculaType::class, $pelicula);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $pelicula->setTitulo($form->get("titulo")->getData());
                $pelicula->setSinopsis($form->get("sinopsis")->getData());

                $ficheroCartel = $form["cartel"]->getData();

                if ($ficheroCartel != null) {
                    $ext = $ficheroCartel->guessExtension();

                    if ($ext == "jpg" || $ext == "png" || $ext == "jpeg") {
                        $nombre_fichero = time() . "." . $ext;
                        $ficheroCartel->move("imagenes/carteles", $nombre_fichero);

                        $pelicula->setCartel($nombre_fichero);
                    } else {
                        $status2 = "Sólo se permiten las extensiones .jpg, .jpeg y .png.";
                        $this->session->getFlashBag()->add("status", $status2);
                        return $this->redirectToRoute("indice_pelicula");
                    }
                } else {
                    $pelicula->setCartel($cartel);
                }

                $ficheroTrailer = $form["trailer"]->getData();

                if ($ficheroTrailer != null) {
                    $ext = $ficheroTrailer->guessExtension();

                    if ($ext == "mp4" || $ext == "avi" || $ext == "wmv" || $ext == "mov") {
                        $nombre_fichero = time() . "." . $ext;
                        $ficheroTrailer->move("imagenes/trailers", $nombre_fichero);

                        $pelicula->setTrailer($nombre_fichero);
                    } else {
                        $status2 = "Sólo se permiten las extensiones .mp4, .avi, .wmv y .mov.";
                        $this->session->getFlashBag()->add("status", $status2);

                        return $this->redirectToRoute("indice_pelicula");
                    }
                } else {
                    $pelicula->setTrailer($trailer);
                }

                $pelicula->setDuracion($form->get("duracion")->getData());
                $pelicula->setIdEdad($form->get("idEdad")->getData());

                $em->persist($pelicula);
                $flush = $em->flush();

                $actorpelicula_repo = $em->getRepository("KarfilmsBundle:Actorpelicula");
                $directorpelicula_repo = $em->getRepository("KarfilmsBundle:Directorpelicula");
                $generopelicula_repo = $em->getRepository("KarfilmsBundle:Generopelicula");

                /*
                 * Funciones para borrar los actores, directores y géneros de las tablas
                 * actorespeliculas, directorespeliculas y generospeliculas, si al editar
                 * la película se ha borrado de esta alguno de ellos.
                 */
                $actorespelicula = $actorpelicula_repo->findBy(["idPelicula" => $pelicula]);

                foreach ($actorespelicula as $ap) {
                    $em->remove($ap);
                    $em->flush();
                }

                $directorespelicula = $directorpelicula_repo->findBy(["idPelicula" => $pelicula]);

                foreach ($directorespelicula as $dp) {
                    $em->remove($dp);
                    $em->flush();
                }

                $generospelicula = $generopelicula_repo->findBy(["idPelicula" => $pelicula]);

                foreach ($generospelicula as $gp) {
                    $em->remove($gp);
                    $em->flush();
                }

                /*
                 * Llamados al repositorio PeliculaRepository.php para añadir los actores,
                 * directores y géneros.
                 */
                $pelicula_repo->guardarActoresPelicula(
                        $form->get("actores")->getData(), $form->get("titulo")->getData()
                );

                $pelicula_repo->guardarDirectoresPelicula(
                        $form->get("directores")->getData(), $form->get("titulo")->getData()
                );

                $pelicula_repo->guardarGenerosPelicula(
                        $form->get("generos")->getData(), $form->get("titulo")->getData()
                );

                if ($flush == null) {
                    $status = "Película editada correctamente.";
                } else {
                    $status = "Error al editar la película.";
                }
            } else {
                $status = "La película no se ha editado porque el formulario no es válido.";
            }

            if ($status == "Película editada correctamente.") {
                $this->session->getFlashBag()->add("status", $status);
                return $this->redirectToRoute("indice_pelicula");
            }
        }

        return $this->render('@Karfilms/pelicula/editarpelicula.html.twig', [
                    "form" => $form->createView(),
                    "cartel" => $cartel,
                    "trailer" => $trailer,
                    "actores" => $actores,
                    "directores" => $directores,
                    "generos" => $generos,
                    "pelicula" => $pelicula,
                    "error" => $error
        ]);
    }

}
