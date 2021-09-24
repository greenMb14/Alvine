<?php

namespace App\Controller;

use App\Entity\Annonce;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CustomerController extends AbstractController
{

    // point d'entrer du site

    #[Route('/', name: 'customer')]
    public function index(): Response
    {
        return $this->render('customer/index.html.twig');
    }











// vusialiastion de tout les blogs

    #[Route('/Blog', name: 'Blog')]

    public function Blog()
    {
        $data = $this->getDoctrine()->getRepository(Annonce::class)->findAll();
        return $this->render('customer/VoirListeBlog.html.twig',["data"=>$data]);
    }

















    
//  en savoir plus sur un blog

#[Route('/EnSavoirPlus/{id}', name: 'EnSavoirPlus')]

public function EnSavoirPlus($id)
{
    $data = $this->getDoctrine()->getRepository(Annonce::class)->findBy(["id"=>$id]);

    // dd($data);

    return $this->render('customer/More.html.twig',["data"=>$data]);
}


}
