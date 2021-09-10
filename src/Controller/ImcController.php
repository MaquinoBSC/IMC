<?php

namespace App\Controller;

use App\Entity\Imc;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ImcController extends AbstractController
{
    /**
     * @Route("/imc", name="imc")
     */
    public function index(Request $request): Response
    {
        //Obtenermos el nombre del usuario del query enviado
        $user= $request->query->get('user');

        //Construimos el formulario
        $imcForm= $this->createFormBuilder()
            ->add("height", IntegerType::class)
            ->add("weight", IntegerType::class)
            ->add("Submit", SubmitType::class)
            ->getForm();

        $imcForm->handleRequest($request);

        //Estamos a la escucha de que el formulario sea enviado
        if($imcForm->isSubmitted() && $imcForm->isValid()){
            $data= $imcForm->getData();

            //hacemos busqueda del usuario en la BD
            $em= $this->getDoctrine()->getManager();
            $userSearch= $em->getRepository(User::class)->findOneBy(['name'=> $user]);

            //Calculamos el IMC
            $subimc1= $data['height']/100;
            $subimc2= $subimc1 * $subimc1;
            $imc= $data['weight'] / $subimc2;
            $date= date('d-m-Y');

            //Validamos que el usuario exista en la BD
            if($userSearch){
                //Obtenemos el registro de IMC del usuario
                $entityManager= $this->getDoctrine()->getManager();
                

                //SI existe IMC, obtenemos el objeto para unicamente actualizarlo
                if($userSearch->getImc()){
                    $imcSearch= $entityManager->getRepository(Imc::class)->find($userSearch->getImc());
                    $imcSearch->setHeight($data['height']);
                    $imcSearch->setWeight($data['weight']);
                    $imcSearch->setDate($date);
                    $imcSearch->setImc((int)$imc);

                    $entityManager->persist($imcSearch);
                    $entityManager->flush();

                    //Actualizamos la relacion de IMC del usuario
                    $userSearch->setImc($imcSearch);
                }
                //Si el No existe IMC, creamos un nuevo objeto IMC y hacemos persistencia a la BD y lo relacionamos con el usuario
                else{
                    $imcObj= new Imc();
                    $imcObj->setHeight($data['height']);
                    $imcObj->setWeight($data['weight']);
                    $imcObj->setDate($date);
                    $imcObj->setImc((int)$imc);

                    $entityManager->persist($imcObj);
                    $entityManager->flush();

                    $userSearch->setImc($imcObj);
                }

                //Hacemos persistencia de los nuevos datos de usuario a la bD
                $em->persist($userSearch);
                $em->flush();
            }
        }

        return $this->render('imc/index.html.twig', [
            'user' => $user,
            'imcForm'=> $imcForm->createView()
        ]);
    }
}
