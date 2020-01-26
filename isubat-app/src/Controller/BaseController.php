<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BaseController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index()
    {
        $user = $this->getUser();

        if(!$user){
            return $this->redirect("/login");
        }

        return $this->render('home/index.html.twig',["login"=>$user->getLogin()]);
    }

    /**
     * @Route("/custom/{name?}",name="custom")
     * @param Request $request
     * @return Response
     */
    public function custom(Request $request)
    {
        dump($request->get('name'));
        $name= $request->get('name');
        return new Response('<h1>TEssST'.$name.'</h1>');
    }
}
class test {
    public function returnString($string){
        return $string;
    }
}