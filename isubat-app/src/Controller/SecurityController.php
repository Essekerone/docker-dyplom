<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @Route("/login", name="login", methods={"GET","POST"})
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    public function index(AuthenticationUtils $authenticationUtils)
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();


        return $this->render('security/index.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    /**
     * @Route("/create",name="create", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function createUser(Request $request)
    {
        // last username entered by the user
        if ($request->isMethod("POST")) {
            $em = $this->getDoctrine()->getManager();
            $login = $request->request->get('login');
            $password = $request->request->get('password');
            $user = new User();
            $user->setLogin($login);
            $user->setPassword($password);
            $password = $this->passwordEncoder->encodePassword($user, $user->getPassword());
            $user->setPassword($password);
            $em->persist($user);
            $em->flush();
        }

        return new Response('<h1>User created</h1>');
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logout()
    {
        return $this->render('security/index.html.twig');
    }

}
