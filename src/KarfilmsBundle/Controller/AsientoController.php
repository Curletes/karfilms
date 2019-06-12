<?php

namespace KarfilmsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use KarfilmsBundle\Entity\Asiento;
use KarfilmsBundle\Form\AsientoType;
use KarfilmsBundle\Form\ReservarAsientoType;
use KarfilmsBundle\Entity\Asientoreservado;

class AsientoController extends Controller {

    private $session;

    public function __construct() {
        $this->session = new Session();
    }

    /*
     * Muestra los asientos que están en la base de datos y paginados 
     * (12 butacas por página).
     */

    public function indiceAsientoAction(Request $request) {
        $em = $this->getDoctrine()->getEntityManager();

        //Consulta para buscar todos los asientos.
        $dql = "SELECT a FROM KarfilmsBundle:Asiento a";
        $query = $em->createQuery($dql);

        //Paginación
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
                $query, $request->query->getInt('page', 1), 12
        );

        //Se buscan los asientos que tienen alguna reserva para evitar que sean borrados
        $asientoreservado_repo = $em->getRepository("KarfilmsBundle:Asientoreservado");
        $asientosreservados = $asientoreservado_repo->findAll();

        return $this->render('@Karfilms/asiento/indiceasiento.html.twig', [
                    "pagination" => $pagination,
                    "asientosreservados" => $asientosreservados
        ]);
    }

    /*
     *  Función para crear un formulario para añadir nuevos asientos a la base de
     *  datos.
     */

    public function addAsientoAction(Request $request) {
        /*
         * Creación del formulario para añadir nuevos asientos, mandando un 
         * objeto Asiento al formulario.
         */
        $asiento = new Asiento();
        $form = $this->createForm(AsientoType::class, $asiento);

        //Recogida de datos enviados por el formulario.
        $form->handleRequest($request);

        //Si se ha enviado el formulario y los datos son válidos, se ejecuta el siguiente código.
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getEntityManager();

                /*
                 * Comprobación de si el asiento que acaba de enviarse por el formulario
                 * ya está en la base de datos, buscándolo según su sala, fila y butaca.
                 * Esto es porque no puede haber dos asientos con la misma fila y el mismo
                 * número en la misma sala.
                 */
                $asiento_repo = $em->getRepository("KarfilmsBundle:Asiento");
                $asiento_existe = $asiento_repo->findOneBy([
                    "idSala" => $form->get("idSala")->getData(),
                    "fila" => $form->get("fila")->getData(),
                    "butaca" => $form->get("butaca")->getData()
                ]);

                /*
                 * Si el resultado de la consulta anterior es null, significa
                 * que no hay ningún asiento registrado con esas características.
                 */
                if ($asiento_existe == null) {
                    /*
                     * Se crea el objeto asiento, llamando a los setters de 
                     * la clase Asiento para añadir la fila, butaca y el id de la sala
                     * que se han enviado por el formulario.
                     */
                    $asiento = new Asiento();
                    $asiento->setFila($form->get("fila")->getData());
                    $asiento->setButaca($form->get("butaca")->getData());
                    $asiento->setIdSala($form->get("idSala")->getData());

                    /*
                     * Se guardan esos sets. Si la variable $flush es nula, el asiento
                     * se ha guardado correctamente.
                     */
                    $em->persist($asiento);
                    $flush = $em->flush();

                    if ($flush == null) {
                        $status = "Asiento añadido correctamente.";
                    }
                } else {
                    $status = "Error ese asiento ya existe.";
                }
            } else {
                $status = "El asiento no se ha añadido porque el formulario no es válido.";
            }

            /*
             * Creación del mensaje que se mostrará para el usuario al haber mandado
             * el formulario. Se redirige a la lista de asientos.
             */
            $this->session->getFlashBag()->add("status", $status);
            return $this->redirectToRoute("indice_asiento");
        }

        //Se envía el formulario a la vista.
        return $this->render('@Karfilms/asiento/addasiento.html.twig', [
                    "form" => $form->createView()
        ]);
    }

    /*
     * Función para eliminar el asiento cuyo id se ha enviado a través de la url
     */
    public function eliminarAsientoAction($id) {
        $em = $this->getDoctrine()->getEntityManager();
        $asiento_repo = $em->getRepository("KarfilmsBundle:Asiento");
        $asiento = $asiento_repo->find($id);

        $em->remove($asiento);
        $em->flush();

        return $this->redirectToRoute("indice_asiento");
    }

    /*
     * Función para editar el asiento cuyo id se ha enviado desde la url.
     * Funcionamiento similar a la función de añadir asientos.
     */
    public function editarAsientoAction($id, Request $request) {
        $em = $this->getDoctrine()->getEntityManager();
        $asiento_repo = $em->getRepository("KarfilmsBundle:Asiento");
        $asiento = $asiento_repo->find($id);

        $form = $this->createForm(AsientoType::class, $asiento);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getEntityManager();

                $asiento_repo = $em->getRepository("KarfilmsBundle:Asiento");
                $asiento_existe = $asiento_repo->findOneBy([
                    "idSala" => $form->get("idSala")->getData(),
                    "fila" => $form->get("fila")->getData(),
                    "butaca" => $form->get("butaca")->getData()
                ]);

                if ($asiento_existe == null) {

                    $asiento->setFila($form->get("fila")->getData());
                    $asiento->setButaca($form->get("butaca")->getData());
                    $asiento->setIdSala($form->get("idSala")->getData());

                    $em->persist($asiento);
                    $flush = $em->flush();

                    if ($flush == null) {
                        $status = "Asiento añadido correctamente.";
                    }
                } else {
                    $status = "Ese asiento ya existe.";
                }
            } else {
                $status = "El asiento no se ha editada porque el formulario no es válido.";
            }

            $this->session->getFlashBag()->add("status", $status);
            return $this->redirectToRoute("indice_asiento");
        }

        return $this->render('@Karfilms/asiento/editarasiento.html.twig', [
                    "form" => $form->createView()
        ]);
    }

    /*
     * Función dedicada a la reserva de las entradas. Recoge el título de la película
     * y su sesión correspondiente. Estos datos han sido enviados a través de la url
     * al seleccionar la hora en la página de inicio.
     */
    public function reservarEntradaAction(Request $request, $pelicula, $sesion) {
        
        //Consulta para recoger los datos de la película y la sesión en cuestión.
        $em = $this->getDoctrine()->getEntityManager();
        $pelicula_repo = $em->getRepository("KarfilmsBundle:Pelicula");
        $pelicula = $pelicula_repo->find(["id" => $pelicula]);

        $sesion_repo = $em->getRepository("KarfilmsBundle:Sesion");
        $sesion = $sesion_repo->find(["id" => $sesion]);

        //Se guarda el id de la sala a la que corresponde dicha sesión
        $sala = $sesion->getIdSala()->getId();

        /* 
         * Creación de formulario para elegir el asiento que se va a reservar,
         * enviando el objeto Asiento y el id de la sala correspondiente.
         */
        $asientos = new Asiento();

        $form = $this->createForm(ReservarAsientoType::class, $asientos, ["idSala" => $sala]);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $asientoreservado = new Asientoreservado();
                $em = $this->getDoctrine()->getEntityManager();
                $asiento_repo = $em->getRepository("KarfilmsBundle:Asiento");

                /*
                 * Se recoge el asiento que ha seleccionado el usuario a través
                 * del formulario.
                 */
                $asiento = $asiento_repo->findOneBy([
                    "fila" => $form->get("fila")->getData()->getFila(),
                    "butaca" => $form->get("butaca")->getData()->getButaca()
                ]);

                /*
                 * Comprobación de que el asiento que ha elegido no esté reservado ya, 
                 * mirando en la entidad Asientorerservado, que es donde se guarda el id
                 * del asiento y el id de la sesión.
                 */
                $reserva_repo = $em->getRepository("KarfilmsBundle:Asientoreservado");
                $reserva = $reserva_repo->findOneBy([
                    "idSesion" => $sesion,
                    "idAsiento" => $asiento
                ]);

                /*
                 * Si la variable $reserva es null, significa que el usuario puede
                 * reservarlo.
                 */
                if ($reserva == null) {
                    /*
                     * Se guarda en la tabla asientoreservado el id del asiento y 
                     * el id de la sesión correctamente.
                     */
                    $asientoreservado->setIdAsiento($asiento);
                    $asientoreservado->setIdSesion($sesion);

                    $em->persist($asientoreservado);
                    $flush = $em->flush();

                    if ($flush == null) {
                        $status = "Asiento reservado correctamente.";
                    }
                } else {
                    $status = "Este asiento ya está reservado. Por favor, elige otro asiento.";
                }
            } else {
                $status = "El asiento no se ha reservado porque el formulario no es válido.";
            }

            /*
             * Si el asiento se ha podido reservar sin problemas, la página redirige a
             * una vista con los datos de la reserva, mostrando la sesión, la sala, 
             * la fila, la butaca y el título de la película.
             * Si no se ha podido reservar, te devuelve a la página de inicio con un mensaje
             * correspondiente.
             */
            $this->session->getFlashBag()->add("status", $status);
            if ($status == "Asiento reservado correctamente.") {
                return $this->render('@Karfilms/reserva/mostrarentrada.html.twig', [
                            "fila" => $asiento->getFila(),
                            "butaca" => $asiento->getButaca(),
                            "pelicula" => $pelicula->getTitulo(),
                            "sesion" => $sesion->getHorarios(),
                            "sala" => $sesion->getIdSala()->getNombre()
                ]);
            } else {
                return $this->redirectToRoute("inicio");
            }
        }
        return $this->render('@Karfilms/reserva/reservarentrada.html.twig', [
                    "pelicula" => $pelicula,
                    "sesion" => $sesion,
                    "form" => $form->createView()
        ]);
    }

    /*
     * Función para el funcionamiento del botón para añadir todos los asientos del cine
     * de una vez, dando por hecho que cada sala de cine tiene 12 filas con 12 asientos
     * cada una.
     * Funciona de forma similar a la función de añadir asientos uno a uno.
     */
    public function addTodosLosAsientosAction(Request $request) {
        $em = $this->getDoctrine()->getEntityManager();

        $sala_repo = $em->getRepository("KarfilmsBundle:Sala");
        $salas = $sala_repo->findAll();
        
        /*
         * Bucles para ir añadiendo 12 filas con 12 butaca a cada sala existente.
         */
        foreach ($salas as $sala) {
            for ($fila = 1; $fila <= 12; $fila++) {
                for ($butaca = 1; $butaca <= 12; $butaca++) {

                    $asiento_repo = $em->getRepository("KarfilmsBundle:Asiento");
                    $asiento_existe = $asiento_repo->findOneBy([
                        "idSala" => $sala,
                        "butaca" => $butaca,
                        "fila" => $fila
                    ]);

                    if ($asiento_existe == null) {
                        $asiento = new Asiento();
                        $asiento->setFila($fila);
                        $asiento->setButaca($butaca);
                        $asiento->setIdSala($sala);

                        $em->persist($asiento);
                        $flush = $em->flush();

                        if ($flush == null) {
                            $status = "Asientos añadidos correctamente.";
                        }
                    } else {
                        $status = "Error. Estos asientos ya existen.";
                    }
                }
            }
        }

        $this->session->getFlashBag()->add("status", $status);
        return $this->redirectToRoute("indice_asiento");
    }

    /*
     * Función para el funcionamiento del botón para eliminar todos los asientos del cine
     * de una vez, dando por hecho que cada sala de cine tiene 12 filas con 12 asientos
     * cada una.
     * Funciona de forma similar a la función de eliminar asientos uno a uno y a la de
     * añadir todos los asientos.
     */
    public function eliminarTodosLosAsientosAction() {
        $em = $this->getDoctrine()->getEntityManager();
        $sala_repo = $em->getRepository("KarfilmsBundle:Sala");
        $salas = $sala_repo->findAll();

        $asiento_repo = $em->getRepository("KarfilmsBundle:Asiento");
        $asientos = $asiento_repo->findAll();

        foreach ($salas as $sala) {
            foreach ($asientos as $asiento) {

                if ($asiento->getIdSala()->getId() == $sala->getId()) {
                    $em->remove($asiento);
                    $em->flush();
                }
            }
        }

        return $this->redirectToRoute("indice_asiento");
    }

}
