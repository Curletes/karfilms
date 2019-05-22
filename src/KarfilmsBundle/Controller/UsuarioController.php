<?php

namespace KarfilmsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use KarfilmsBundle\Entity\Usuario;
use KarfilmsBundle\Form\UsuarioType;
use KarfilmsBundle\Form\EditarUsuarioType;

class UsuarioController extends Controller {

    private $session;

    public function __construct() {
        $this->session = new Session();
    }

    public function iniciarSesionAction(Request $request) {
        $authenticationUtils = $this->get("security.authentication_utils");
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('@Karfilms/usuario/iniciosesion.html.twig', [
                    'error' => $error,
                    'last_username' => $lastUsername
        ]);
    }

    public function registrarseAction(Request $request) {
        $authenticationUtils = $this->get("security.authentication_utils");
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        $usuario = new Usuario();
        $form = $this->createForm(UsuarioType::class, $usuario);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getEntityManager();
                $repositorioUsuario = $em->getRepository("KarfilmsBundle:Usuario");
                $usuarioemail = $repositorioUsuario->findOneBy(array("email" => $form->get("email")->getData()));

                $usuarionombre = $repositorioUsuario->findOneBy(array("nombre" => $form->get("nombre")->getData()));

                if ($usuarioemail == NULL && $usuarionombre == NULL) {
                    $usuario = new Usuario();
                    $usuario->setNombre($form->get("nombre")->getData());
                    $usuario->setEmail($form->get("email")->getData());

                    $factory = $this->get("security.encoder_factory");
                    $encoder = $factory->getEncoder($usuario);
                    $password = $encoder->encodePassword($form->get("password")->getData(), $usuario->getSalt());

                    $usuario->setPassword($password);

                    $em = $this->getDoctrine()->getEntityManager();
                    $em->persist($usuario);
                    $flush = $em->flush();

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
                return $this->redirectToRoute("iniciar_sesion");
            }
        }

        return $this->render('@Karfilms/usuario/registrarse.html.twig', [
                    'error' => $error,
                    'last_username' => $lastUsername,
                    'form' => $form->createView()
        ]);
    }

    public function indiceUsuarioAction() {
        $em = $this->getDoctrine()->getEntityManager();
        $usuario_repo = $em->getRepository("KarfilmsBundle:Usuario");
        $usuarios = $usuario_repo->findAll();

        return $this->render('@Karfilms/usuario/indiceusuario.html.twig', [
                    "usuarios" => $usuarios
        ]);
    }

    public function eliminarUsuarioAction($id) {
        $em = $this->getDoctrine()->getEntityManager();
        $usuario_repo = $em->getRepository("KarfilmsBundle:Usuario");
        $usuario = $usuario_repo->find($id);

        $em->remove($usuario);
        $em->flush();

        return $this->redirectToRoute("indice_usuario");
    }

    public function editarUsuarioAction($id, Request $request) {
        $em = $this->getDoctrine()->getEntityManager();
        $usuario_repo = $em->getRepository("KarfilmsBundle:Usuario");
        $usuario = $usuario_repo->find($id);
        $icono = $usuario->getIcono();
        $password = $usuario->getPassword();
        $form = $this->createForm(EditarUsuarioType::class, $usuario);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $usuario->setNombre($form->get("nombre")->getData());
                $usuario->setEmail($form->get("email")->getData());

                $fichero = $form["icono"]->getData();
                
                if ($fichero != null) {
                    $ext = $fichero->guessExtension();

                    if ($ext == "jpg" || $ext == "png" || $ext == "jpeg") {
                        $nombre_fichero = time() . "." . $ext;
                        $fichero->move("imagenes/perfiles", $nombre_fichero);

                        $usuario->setIcono($nombre_fichero);
                    }
                    else {
                        $usuario->setIcono($icono);
                        $status2 = "Sólo se permiten las extensiones .jpg, .jpeg y .png.";
                    }
                } else {
                    $usuario->setIcono($icono);
                }

                $password2 = $form["password"]->getData();

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
                    if(isset($status2)) {
                        $status = $status2;
                    }
                    else {
                        $status = "Perfil editado correctamente.";
                    }     
                } else {
                    $status = "Error al editar el perfil.";
                }
            } else {
                $status = "El perfil no se ha editado porque el formulario no es válido.";
            }

            $this->session->getFlashBag()->add("status", $status);
            return $this->redirectToRoute("perfil_usuario", ["id" => $id]);
        }

        return $this->render('@Karfilms/usuario/editarusuario.html.twig', [
                    "form" => $form->createView(),
                    "icono" => $icono,
                    "id" => $id
        ]);
    }

    public function administradorAction($id) {
        $em = $this->getDoctrine()->getEntityManager();
        $usuario_repo = $em->getRepository("KarfilmsBundle:Usuario");
        $usuario = $usuario_repo->find($id);

        if ($usuario->getRol() == "ROLE_USER") {
            $usuario->setRol("ROLE_ADMIN");
        } else {
            $usuario->setRol("ROLE_USER");
        }

        $em->persist($usuario);
        $em->flush();

        return $this->redirectToRoute("indice_usuario");
    }

    public function perfilUsuarioAction($id) {
        $em = $this->getDoctrine()->getEntityManager();
        $usuario_repo = $em->getRepository("KarfilmsBundle:Usuario");
        $usuario = $usuario_repo->find($id);

        return $this->render('@Karfilms/usuario/perfilusuario.html.twig', [
                    "usuario" => $usuario,
                    "id" => $id
        ]);
    }
}
