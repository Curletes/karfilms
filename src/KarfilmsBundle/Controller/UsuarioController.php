<?php

namespace KarfilmsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use KarfilmsBundle\Entity\Usuario;
use KarfilmsBundle\Form\UsuarioType;
use KarfilmsBundle\Form\EditarUsuarioType;
use KarfilmsBundle\Form\CambiarIconoType;

class UsuarioController extends Controller {

    private $session;

    public function __construct() {
        $this->session = new Session();
    }

    /*
     * Función para iniciar sesión, recogiendo los datos que ha enviado el usuario
     * a través del formulario.
     */

    public function iniciarSesionAction(Request $request) {
        /*
         * Comprobaciones de que los datos coinciden con los del usuario registrado
         * en la base de datos. Si no son correctos, se vuelve a cargar el formulario
         * de inicio de sesión, permaneciendo el nombre de usuario que se ha escrito en
         * el formulario.
         */
        $authenticationUtils = $this->get("security.authentication_utils");
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('@Karfilms/usuario/iniciosesion.html.twig', [
                    'error' => $error,
                    'last_username' => $lastUsername
        ]);
    }

    /*
     * Función para crear un formulario para añadir nuevos usuarios a la base de
     * datos.
     */

    public function registrarseAction(Request $request) {
        $authenticationUtils = $this->get("security.authentication_utils");
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        /*
         * Se crea un objeto usuario nuevo y se manda con el formulario para que
         * muestre los campos de la entidad que tienen que rellenarse.
         */
        $usuario = new Usuario();
        $form = $this->createForm(UsuarioType::class, $usuario);

        //Se recogen los datos enviados desde el formulario.
        $form->handleRequest($request);

        //Esta parte de la función se ejecuta cuando el formulario se ha enviado y es válido.
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getEntityManager();
                $repositorioUsuario = $em->getRepository("KarfilmsBundle:Usuario");
                /*
                 * Se comprueba que el email y el usuario introducidos no estén registrados
                 * ya en la base de datos.
                 */
                $usuarioemail = $repositorioUsuario->findOneBy(array("email" => $form->get("email")->getData()));

                $usuarionombre = $repositorioUsuario->findOneBy(array("nombre" => $form->get("nombre")->getData()));

                if ($usuarioemail == NULL && $usuarionombre == NULL) {
                    $usuario = new Usuario();
                    /*
                     * Si ni el email ni el usuario existen, se hace un set en
                     * la entidad Usuario con el nombre, email y contraseña introdudidos
                     * en el formulario y se guarda con persist y flush.
                     */
                    $usuario->setNombre($form->get("nombre")->getData());
                    $usuario->setEmail($form->get("email")->getData());

                    /*
                     * Código para cifrar la contraseña en la base de datos.
                     */
                    $factory = $this->get("security.encoder_factory");
                    $encoder = $factory->getEncoder($usuario);
                    $password = $encoder->encodePassword($form->get("password")->getData(), $usuario->getSalt());

                    $usuario->setPassword($password);

                    $em = $this->getDoctrine()->getEntityManager();
                    $em->persist($usuario);
                    $flush = $em->flush();

                    //Si la variable flush está vacía, significa que los datos se han añadido sin problema.
                    if ($flush == null) {
                        $status = "Registrado correctamente.";
                    } else {
                        $status = "Error al registrarse.";
                    }
                } elseif ($usuarioemail != NULL) {
                    $status = "Ese email ya existe.";
                } else {
                    $status = "Ese nombre de usuario ya existe.";
                }
            } else {
                $status = "Error al registrarse.";
            }

            $this->session->getFlashBag()->add("status", $status);

            if ($status == "Registrado correctamente.") {
                /*
                 * Se envía a la vista el mensaje creado y guardado en la variable status,
                 * y redirige hacia el formulario para iniciar sesión.
                 */
                return $this->redirectToRoute("iniciar_sesion");
            }
        }

        /*
         * Si no se ha registrado correctamente, se manda el mensaje de error
         * correspondiente.
         */
        return $this->render('@Karfilms/usuario/registrarse.html.twig', [
                    'error' => $error,
                    'last_username' => $lastUsername,
                    'form' => $form->createView()
        ]);
    }

    /*
     * Muestra los usuarios que están en la base de datos, listándolos por orden
     * alfabético y paginados (5 usuarios por página).
     */

    public function indiceUsuarioAction(Request $request) {
        $em = $this->getDoctrine()->getEntityManager();

        /*
         * Creación de una query para realizar una consulta a la base de datos.
         * Selecciona todos los usuarios por orden alfabético.
         */
        $dql = "SELECT u FROM KarfilmsBundle:Usuario u ORDER BY u.nombre ASC";
        $query = $em->createQuery($dql);

        /*
         * Paginación
         */
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
                $query, $request->query->getInt('page', 1), 5
        );

        return $this->render('@Karfilms/usuario/indiceusuario.html.twig', [
                    "pagination" => $pagination
        ]);
    }

    //Método para eliminar usuarios, reconociendo el usuario en específico por el id enviado desde la url
    public function eliminarUsuarioAction($id) {
        $em = $this->getDoctrine()->getEntityManager();
        $usuario_repo = $em->getRepository("KarfilmsBundle:Usuario");
        $usuario = $usuario_repo->find($id);

        /*
         * Comprobación de si el usuario ha enviado sugerencias o ha valorado alguna.
         * Si se da el caso, las valoraciones y/o sugerencias se eliminan para
         * después eliminar al usuario.
         */
        $sugerencia_repo = $em->getRepository("KarfilmsBundle:Sugerencia");
        $sugerencias = $sugerencia_repo->findAll();

        $valoracion_repo = $em->getRepository("KarfilmsBundle:Valoracion");
        $valoraciones = $valoracion_repo->findAll();

        foreach ($sugerencias as $sugerencia) {
            if ($sugerencia->getIdUsuario()->getId() == $id) {
                $em->remove($sugerencia);
                $em->flush();
            }
        }

        foreach ($valoraciones as $valoracion) {
            if ($valoracion->getIdUsuario()->getId() == $id) {
                $em->remove($valoracion);
                $em->flush();
            }
        }

        $em->remove($usuario);
        $em->flush();

        return $this->redirectToRoute("indice_usuario");
    }

    /*
     * Función para editar tu propio perfil de usuario, recogiendo el id del
     * usuario en cuestión enviado por la url y también los datos enviados desde
     * el formulario de edición con la variable $request.
     * Funcionamiento similar al de la función para añadir usuarios.
     */

    public function editarUsuarioAction($id, Request $request) {
        $authenticationUtils = $this->get("security.authentication_utils");
        $error = $authenticationUtils->getLastAuthenticationError();
        $em = $this->getDoctrine()->getEntityManager();
        $usuario_repo = $em->getRepository("KarfilmsBundle:Usuario");
        $usuario = $usuario_repo->find($id);
        /*
         * Se guarda la contraseña del usuario en una variable. Esto es para 
         * volver a asignar esta contraseña si el usuario ha enviado el formulario
         * y ha decidido no cambiarla.
         */
        $password = $usuario->getPassword();
        $form = $this->createForm(EditarUsuarioType::class, $usuario);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $usuario->setNombre($form->get("nombre")->getData());
                $usuario->setEmail($form->get("email")->getData());

                /*
                 * Se guarda en una variable la contraseña enviada por el formulario.
                 */
                $password2 = $form["password"]->getData();

                /*
                 * Si el usuario ha escrito una nueva contraseña, se vuelve a codificar
                 * y se guarda en la base de datos. Si no, se vuelve a guardar en la
                 * base de datos la contraseña que ya tenía asignada el usuario anteriormente.
                 */
                if ($password2 != null) {
                    $factory = $this->get("security.encoder_factory");
                    $encoder = $factory->getEncoder($usuario);
                    $password2 = $encoder->encodePassword($form->get("password")->getData(), $usuario->getSalt());

                    $usuario->setPassword($password2);
                } else {
                    $usuario->setPassword($password);
                }

                $em->persist($usuario);
                $flush = $em->flush();

                if ($flush == null) {
                    $status = "Perfil editado correctamente.";
                } else {
                    $status = "Error al editar el perfil.";
                }
            } else {
                $status = "El perfil no se ha editado porque el formulario no es válido.";
            }
            if ($status == "Perfil editado correctamente.") {
                $this->session->getFlashBag()->add("status", $status);
                return $this->redirectToRoute("mi_perfil", ["id" => $id]);
            }
        }

        return $this->render('@Karfilms/usuario/editarusuario.html.twig', [
                    "form" => $form->createView(),
                    "id" => $id,
                    "error" => $error
        ]);
    }

    /*
     * Función para cambiar tu icono de usuario, reconociendo el usuario a través
     * del id enviado por la url.
     */

    public function cambiarIconoAction($id, Request $request) {
        $em = $this->getDoctrine()->getEntityManager();
        $usuario_repo = $em->getRepository("KarfilmsBundle:Usuario");
        $usuario = $usuario_repo->find($id);
        /*
         * Se guarda en una variable el icono que tiene el usuario para asignárselo
         * de nuevo si el usuario ha enviado el formulario sin añadir un icono nuevo.
         */
        $icono = $usuario->getIcono();

        $form = $this->createForm(CambiarIconoType::class, $usuario);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {

                /*
                 * Se recoge la imagen que se ha enviado y se guarda en la variable
                 * $fichero.
                 */
                $fichero = $form["icono"]->getData();

                if ($fichero != null) {
                    /*
                     * Se guarda la extensión del fichero en una variable para 
                     * comprobar que los datos son correctos.
                     */
                    $ext = $fichero->guessExtension();

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
                        $fichero->move("imagenes/perfiles", $nombre_fichero);

                        /*
                         * Se hace un set para guardar en la base de datos el nombre del fichero.
                         */
                        $usuario->setIcono($nombre_fichero);
                    } else {
                        /*
                         * Si la extensión del fichero no es correcta, se mandará
                         * un mensaje de error al usuario y lo devolverá al perfil
                         * del usuario.
                         * Además se mantendrá el anterior icono que tuviera asignado
                         * el usuario.
                         */
                        $usuario->setIcono($icono);
                        $status = "Sólo se permiten las extensiones .jpg, .jpeg y .png.";
                        $this->session->getFlashBag()->add("status", $status);
                        return $this->redirectToRoute("mi_perfil", ["id" => $id]);
                    }
                } else {
                    $usuario->setIcono($icono);
                }

                $em->persist($usuario);
                $flush = $em->flush();

                if ($flush == null) {
                    $status = "Perfil editado correctamente.";
                } else {
                    $status = "Error al editar el perfil.";
                }
            } else {
                $status = "El perfil no se ha editado porque el formulario no es válido.";
            }

            /*
             * Redirección al perfil del usuario.
             */
            $this->session->getFlashBag()->add("status", $status);
            return $this->redirectToRoute("mi_perfil", ["id" => $id]);
        }

        return $this->render('@Karfilms/usuario/cambiaricono.html.twig', [
                    "usuario" => $usuario,
                    "cambiar_icono" => $form->createView(),
                    "icono" => $icono,
                    "id" => $id
        ]);
    }

    /*
     * Función para asignar el rol administrador al usuario especificado, reconociéndolo
     * por el id enviado desde la url al pulsar le botón.
     */

    public function administradorAction($id) {
        $em = $this->getDoctrine()->getEntityManager();
        $usuario_repo = $em->getRepository("KarfilmsBundle:Usuario");
        $usuario = $usuario_repo->find($id);

        /*
         * Si el rol del usuario es ROLE_USER, se asigna ROLE_ADMIN y viceversa.
         */
        if ($usuario->getRol() == "ROLE_USER") {
            $usuario->setRol("ROLE_ADMIN");
        } else {
            $usuario->setRol("ROLE_USER");
        }

        $em->persist($usuario);
        $em->flush();

        return $this->redirectToRoute("indice_usuario");
    }

    /*
     * Función para mostrar tu perfil, junto a tus sugerencias.
     */

    public function miPerfilAction($id) {
        $em = $this->getDoctrine()->getEntityManager();
        $usuario_repo = $em->getRepository("KarfilmsBundle:Usuario");
        $usuario = $usuario_repo->find($id);

        $sugerencia_repo = $em->getRepository("KarfilmsBundle:Sugerencia");
        $sugerencias = $sugerencia_repo->findBy(["idUsuario" => $id]);

        return $this->render('@Karfilms/usuario/miperfil.html.twig', [
                    "usuario" => $usuario,
                    "sugerencias" => $sugerencias,
                    "id" => $id
        ]);
    }

    /*
     * Función para mostrar el perfil de usuario seleccionado, junto a sus sugerencias.
     */

    public function perfilUsuarioAction($id) {
        $em = $this->getDoctrine()->getEntityManager();
        $usuario_repo = $em->getRepository("KarfilmsBundle:Usuario");
        $usuario = $usuario_repo->find($id);

        $sugerencia_repo = $em->getRepository("KarfilmsBundle:Sugerencia");
        $sugerencias = $sugerencia_repo->findBy(["idUsuario" => $id]);

        return $this->render('@Karfilms/usuario/perfilusuario.html.twig', [
                    "usuario" => $usuario,
                    "sugerencias" => $sugerencias,
                    "id" => $id
        ]);
    }

}
