<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace App\Controller\admin;

use App\Entity\Categorie;
use App\Entity\Formation;
use App\Entity\Playlist;
use App\Form\CategorieType;
use App\Form\PlaylistType;
use App\Repository\CategorieRepository;
use App\Repository\FormationRepository;
use App\Repository\PlaylistRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Description of AdminMediatekcategorieController
 *
 * @author neeld
 */
class AdminMediatekcategorieController extends AbstractController  {   
    
    /**
     * 
     * @var CategorieRepository
     */
    private $categorieRepository;
    
    function __construct(  CategorieRepository $categorieRepository) {
        
        $this->categorieRepository= $categorieRepository;
        
    }
    
    /**
     * @Route("/admin/categorie", name="admin.categorie")
     * @return Response
     */
    public function index(): Response{       
        $categories = $this->categorieRepository->findAll();
        return $this->render("admin/admin.categorie.html.twig", [            
            'categories' => $categories
        ]);
    }
    
     /**
     * @Route("/admin/suppr/categorie/{id}", name="admin.categorie.suppr")
     * @param Categorie $categorie
     * @return Response
     */
    public function suppr(Categorie $categorie): Response{
        $this->categorieRepository->remove($categorie, true);
        return $this->redirectToRoute('admin.categorie');
    }
    
    /**
     * @Route("/admin/ajout/categorie}", name="admin.categorie.ajout")     
     * @param Request $request
     * @return Response
     */
    public function ajout(Request $request): Response{
        $categorie = new Categorie();
        $formCategorie = $this->createForm(CategorieType::class, $categorie);
        
        $formCategorie->handleRequest($request);       
            
        if($formCategorie->isSubmitted() && $formCategorie->isValid()){
            $this->categorieRepository->add($categorie, true);
            return $this->redirectToRoute('admin.categorie');
        }
           
        return $this->render("admin/admin.categorie.ajout.html.twig", [
            'categorie' => $categorie,
            'formCategorie' => $formCategorie->createView()
        ]); 
    }
}
