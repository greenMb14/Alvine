<?php

namespace App\Controller;

use App\Entity\Annonce;
use App\Form\AnnonceType;
use PhpParser\Node\Stmt\TryCatch;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class HomeAdminController extends AbstractController
{







// entrer dans l'espace d'administrateur 

    #[Route('/home/admin', name: 'home_admin')]
    public function index(): Response
    {
        return $this->render('home_admin/index.html.twig');
    }
















//redirection  , traitement et sauvegarde d'une annonce

    #[Route('/PosterUneAnnonce', name: 'PosterUneAnnonce')]
    public function PosterUneAnnonce(Request $request) 
    {
        $Annonce = new Annonce();

        $entityManager = $this->getDoctrine()->getManager();

        $form = $this->createForm(AnnonceType::class, $Annonce);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            // recuperation des eleement du formulaire


            $title =  $Annonce->getTitle();
            $category =  $Annonce->getCategory();
            $resumer =  $Annonce->getResumer();
            $firstTitle =  $Annonce->getFirstTitle();
            $contentA =  $Annonce->getContentA();
            $secondTitle =  $Annonce->getSecondTitle();
            $contentB =  $Annonce->getContentB();
            $thirtTitle =  $Annonce->getThirtTitle();
            $contentC =  $Annonce->getContentC();
            $file = $Annonce->getFile();


            try {

           
                $filename = md5(uniqid()).'.'.$file->guessExtension();
                $file->move($this->getParameter('upload_destination'),$filename);
                
                // enregistrement des element du formulaire
            
                $Annonce->setTitle($title);
                $Annonce->setCategory($category);
                $Annonce->setFile($filename);
                $Annonce->setResumer($resumer);
                $Annonce->setFirstTitle($firstTitle);
                $Annonce->setContentA($contentA);
                $Annonce->setSecondTitle($secondTitle);
                $Annonce->setContentB($contentB);
                $Annonce->setThirtTitle($thirtTitle);
                $Annonce->setContentC($contentC);
                $entityManager->persist($Annonce);
                $entityManager->flush();

        // cas de l'operation d'enregistrement avec succes 

                $this->addFlash(
                    'Success',
                    'votre annonce a ete bien enregistrer'
                );

          return $this->render('home_admin/FormulaireAnnonce.html.twig',['form'=>$form->createView()]);  
                

            } catch (FileException $th) {
                
                $this->addFlash(
                    'errorData',
                    "une erreur c'est prduit lors du chargement de vos donnees"
                 );
    // cas d'une erreur de chargement de fichier

     return $this->render('home_admin/FormulaireAnnonce.html.twig',['form'=>$form->createView()]); 
            }

 
        
        }else {
            $this->addFlash(
               'errorSaisie',
               'veuillez remplir correctement les champs '
            );

     // cas de mauvaise saisie des champs du formulaire

        return $this->render('home_admin/FormulaireAnnonce.html.twig',['form'=>$form->createView()]); 
        }

// creation du formulaire d'annonce
        return $this->render('home_admin/FormulaireAnnonce.html.twig',['form'=>$form->createView()]);
    }
















    //liste de tout les annonces 

    #[Route('/ListeAnnonce', name: 'ListeAnnonce')]

    public function TListeAnnonce(Request $request) 
    {
  
         $data = $this->getDoctrine()->getRepository(Annonce::class)->findAll();
        return $this->render('home_admin/ListeDannonce.html.twig',['data'=>$data]);
    }



















    
    
    //Suppression d'une annonces 

    #[Route('/DeleteAnnonce/{id}', name: 'DeleteAnnonce')]

    public function DeleteAnnonce(Request $request,$id) 
    {

        $entityManager = $this->getDoctrine()->getManager();
        $Annonce = $entityManager->getRepository(Annonce::class)->findOneBy(["id"=>$id]);

        if (!$Annonce) {
            
            $this->addFlash("error","l'annonce n'a pas pu etre trouver, veuillez reesaiyer");

        }else {

            $entityManager->remove($Annonce);
            $entityManager->flush();
            $this->addFlash("success","votre annonce a bien ete supprimer!!!");
            return $this->redirectToRoute('ListeAnnonce');

        }
       
    }



















        //Modifier une annonce

        #[Route('/UpdateAnnonce/{id}', name: 'UpdateAnnonce')]

        public function UpdateAnnonce(Request $request,$id) 
        {
    
            $entityManager = $this->getDoctrine()->getManager();
            $Annonce =  $this->getDoctrine()->getRepository(Annonce::class)->find($id);
    
            if (!$Annonce) {
                
                $this->addFlash("error","l'annonce n'a pas pu etre trouver, veuillez reesaiyer");
                return $this->redirectToRoute('ListeAnnonce');
    
            }else {
               $form = $this->createFormBuilder($Annonce)
               ->add('title', TextType::class)
               ->add('category', TextType::class)
               ->add('resumer', TextareaType::class)
               ->add('firstTitle', TextType::class)
               ->add('ContentA', TextareaType::class)
               ->add('secondTitle', TextType::class , array('required' => false,))
               ->add('ContentB', TextareaType::class , array('required' => false,))
               ->add('thirtTitle', TextType::class , array('required' => false,))
               ->add('ContentC', TextareaType::class, array('required' => false,))
               ->add("Poster", SubmitType::class)
               ->getForm()  
               ;
               $form->handleRequest($request);

               if ($form->isSubmitted() && $form->isValid()) {

                   $Update = $form->getData();
                   $entityManager->flush();
                   $this->addFlash("success","votre annonce a bien ete modifier!!!");
                   return $this->redirectToRoute('ListeAnnonce');
                   
               }
             
               $this->addFlash(
                'errorSaisie',
                'veuillez remplir correctement les champs '
               );
            //    dd($Annonce);
               return $this->render('home_admin/UpdateAnnonce.html.twig',["form"=>$form->createView(),"img"=>$Annonce,]);
           
                
            }
           
        }



















            
    // retour sur le dashbord statistique sur les blogs 

    #[Route('/Statistique', name: 'Statistique')]

    public function Statistique() 
    {

         return $this->render('home_admin/Statistique.html.twig');
     
    }




















        //liate de tout les commentaires 

        #[Route('/ListeCommentaire', name: 'ListeCommentaire')]

        public function ListeCommentaire() 
        {
              
             return $this->render('home_admin/ListeCommentaire.html.twig');
         
        }




















           //liste des likes 

           #[Route('/ListeLike', name: 'ListeLike')]

           public function Listelike() 
           {
                 
                return $this->render('home_admin/ListeLike.html.twig');
            
           }






















            //liste des Unlikes 

            #[Route('/ListeUnLike', name: 'ListeUnLike')]

            public function ListeUnlike() 
            {
                  
                 return $this->render('home_admin/ListeUnLike.html.twig');
             
            }

}
