<?php

namespace KarfilmsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use KarfilmsBundle\Entity\Usuario;
use KarfilmsBundle\Form\UsuarioType;

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

            if ($status != "Registrado correctamente.") {
                return $this->redirectToRoute("registrarse");
            } else {
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
        $rol = $usuario->getRol();
        $icono = $usuario->getIcono();

        $form = $this->createForm(UsuarioType::class, $usuario);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $usuario->setNombre($form->get("nombre")->getData());
                $usuario->setEmail($form->get("email")->getData());
                
                if(isset($_POST['admin']))
                {
                    $usuario->setRol("ROLE_ADMIN");
                }
                else
                {
                    $usuario->setRol("ROLE_USER");
                }
                
                $factory = $this->get("security.encoder_factory");
                $encoder = $factory->getEncoder($usuario);
                $password = $encoder->encodePassword($form->get("password")->getData(), $usuario->getSalt());

                $usuario->setPassword($password);

                $em->persist($usuario);
                $flush = $em->flush();

                if ($flush == null) {
                    $status = "Usuario editado correctamente.";
                } else {
                    $status = "Error al editar el usuario.";
                }
            } else {
                $status = "El usuario no se ha editado porque el formulario no es válido.";
            }

            $this->session->getFlashBag()->add("status", $status);
            return $this->redirectToRoute("indice_usuario");
        }

        return $this->render('@Karfilms/usuario/editarusuario.html.twig', [
                    "form" => $form->createView(),
                    "rol" => $rol,
                    "icono" => $icono
        ]);
    }
}
