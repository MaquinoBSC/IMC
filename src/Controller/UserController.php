<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @Route("/register", name="register")
     */
    public function register(Request $request): Response
    {
        $registerForm= $this->createFormBuilder()
            ->add("name")
            ->add("email", EmailType::class)
            ->add("password", PasswordType::class)
            ->add("Submit", SubmitType::class)
            ->getForm();

        $registerForm->handleRequest($request);

        if($registerForm->isSubmitted() && $registerForm->isValid()){
            $data= $registerForm->getData();

            $user= new User();
            $user->setName($data['name']);
            $user->setEmail($data['email']);
            $user->setPassword($data['password']);

            $entityManager= $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('imc', [
                'user'=> $data['name'],
            ]);
        }

        return $this->render('user/register.html.twig', [
            'controller_name' => 'Register',
            'registerForm'=> $registerForm->createView()
        ]);
    }

    /**
     * @Route("/", name="login")
     */
    public function login(Request $request): Response
    {
        $loginForm= $this->createFormBuilder()
            ->add("name")
            ->add("email", EmailType::class)
            ->add("password", PasswordType::class)
            ->add("Submit", SubmitType::class)
            ->getForm();

        $loginForm->handleRequest($request);

        if($loginForm->isSubmitted() && $loginForm->isValid()){
            $data= $loginForm->getData();

            $searchUser= $this->getDoctrine()
                ->getRepository(User::class)
                ->LoginUser($data['name']);

            if($searchUser){
                if($searchUser[0]['email'] == $data['email'] && $searchUser[0]['password'] == $data['password']){
                    return $this->redirectToRoute('imc', [
                        'user'=> $searchUser[0]['name'],
                    ]);
                }
                else{
                    return $this->render('user/result.html.twig', [
                        'text'=> "Usuario no autorizado, veirifca tus credenciales",
                        'user'=> $searchUser,
                    ]);
                }
            }
            else{
                return $this->render('user/result.html.twig', [
                    'text'=> "Usuario NO encontrado",
                    'user'=> $searchUser,
                ]);
            }
        }

        return $this->render('user/login.html.twig', [
            'controller_name' => 'Log In',
            'loginForm'=> $loginForm->createView()
        ]);
    }
}
