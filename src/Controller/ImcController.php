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
        $user= $request->query->get('user');

        $imcForm= $this->createFormBuilder()
            ->add("height", IntegerType::class)
            ->add("weight", IntegerType::class)
            ->add("Submit", SubmitType::class)
            ->getForm();

        $imcForm->handleRequest($request);

        if($imcForm->isSubmitted() && $imcForm->isValid()){
            $data= $imcForm->getData();

            $subimc1= $data['height']/100;
            $subimc2= $subimc1 * $subimc1;
            $imc= $data['weight'] / $subimc2;
            
            $date= date('d-m-Y');

            $imcObj= new Imc();
            $imcObj->setHeight($data['height']);
            $imcObj->setWeight($data['weight']);
            $imcObj->setDate($date);
            $imcObj->setImc((int)$imc);

            $em= $this->getDoctrine()->getManager();
            $userSearch= $em->getRepository(User::class)->findOneBy(['name'=> $user]);

            if($userSearch){
                $entityManager= $this->getDoctrine()->getManager();
                $entityManager->persist($imcObj);
                $entityManager->flush();

                $userSearch->setImc($imcObj);

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
