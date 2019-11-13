<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(Request $request)
    {

        $finder=new Finder();
        $finder->directories()->in("../public/photo");
        $form = $this->createFormBuilder()
            ->add('Nom', TextType::class, ['label'=>'Nom de dossier : '])
            ->add('ajouter',SubmitType::class, ['label'=>'Envoyer', 'attr'=>["class"=>'btn blue-primary']])
            ->getForm();
        $form->handleRequest($request);

        $dataDossier=$form->getData();       //recupere le tableau, je ne veux que une valeur
        if ($dataDossier["Nom"] != null){
            $filesystem = new Filesystem();
            $filesystem->mkdir('../public/photo/'.$dataDossier["Nom"]);
        }

        return $this->render('home/index.html.twig', [
            "dossiers"=>$finder,
            "formulaire"=>$form->createView(),
        ]);
    }

    /**
     * @Route("/jemetscequejeveux/{nomDuDossier}", name="dossier")
     */
    public function afficherDossier($nomDuDossier, Request $request)
    {
        $finder=new Finder();
        $finder->files()->in("../public/photo/".urldecode($nomDuDossier));

        //Creation d'un formulaire
        $form = $this->createFormBuilder()
            ->add('photo', FileType::class, ['label'=>'Ajouter un chaton'])
            ->add('ajouter',SubmitType::class, ['label'=>'Envoyer', 'attr'=>["class"=>'btn blue-primary']])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $data=$form->getData();
            $fichiers="../public/photo/".urldecode($nomDuDossier);
            $extension = 0;
            $name = $data['photo']->getClientOriginalName();
            $actual_name = $name;

            foreach (scandir($fichiers) as $item) {
                if ($name == $item){
                    $extension++;
                    $name = $actual_name.'.'.$extension;
                }
            }
            $data["photo"]->move("../public/photo/" . urldecode($nomDuDossier), $name);
        }


        return $this->render('home/afficherDossier.html.twig', [
            "nomDuDossier"=>urldecode($nomDuDossier),
            "fichiers"=>$finder,
            "formulaire"=>$form->createView(),
        ]);
    }








}
