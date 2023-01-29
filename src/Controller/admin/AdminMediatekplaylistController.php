<?php
namespace App\Controller\admin;

use App\Entity\Formation;
use App\Entity\Playlist;
use App\Form\PlaylistType;
use App\Repository\CategorieRepository;
use App\Repository\FormationRepository;
use App\Repository\PlaylistRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of AdminMediatekplaylistController
 *
 * @author neeld
 */
class AdminMediatekplaylistController extends AbstractController {
    /**
     * 
     * @var PlaylistRepository
     */
    private $playlistRepository;
    
    /**
     * 
     * @var FormationRepository
     */
    private $formationRepository;
    
    /**
     * 
     * @var CategorieRepository
     */
    private $categorieRepository;
    
    function __construct(PlaylistRepository $playlistRepository, CategorieRepository $categorieRepository, FormationRepository $formationRepository) {
        $this->playlistRepository = $playlistRepository;
        $this->categorieRepository= $categorieRepository;
        $this->formationRepository= $formationRepository;
    }
    
    /**
     * @Route("/admin/playlist", name="admin.playlist")
     * @return Response
     */
    public function index(): Response{
        $playlists = $this->playlistRepository->findAll();
        $categories = $this->categorieRepository->findAll();
        return $this->render("admin/admin.playlist.html.twig", [
            'playlists' => $playlists,
            'categories' => $categories
        ]);
    }
    
    /**
     * @Route("/admin/suppr/playlist/{id}", name="admin.playlist.suppr")
     * @param Playlist $playlist
     * @return Response
     */
    public function suppr(Playlist $playlist): Response{
        $this->playlistRepository->remove($playlist, true);
        return $this->redirectToRoute('admin.playlist');
    }
    
    /**
     * @Route("/admin/playlists/tri/{champ}/{ordre}/{table}", name="admin.playlists.sort")
     * @param type $champ
     * @param type $ordre
     * @param type $table
     * @return Response
     */
    public function sort($ordre): Response{
        
            $playlist = $this->playlistRepository->findAllOrderByName($ordre);
        
        return $this->render("admin/admin.playlist.html.twig", [
            'playlists' => $playlist,
        ]);
    }
    
     /**
     * @Route("/admin/playlists/recherche/{champ}/{table}", name="admin.playlists.findallcontain")
     * @param type $champ
     * @param Request $request
     * @param type $table
     * @return Response
     */
    public function findAllContain($champ, Request $request, $table=""): Response{
        $valeur = $request->get("recherche");
        if($table==""){
           $playlist = $this->playlistRepository->findByContainValue($champ, $valeur); 
        }else{
            $playlist = $this->playlistRepository->findByContainValueWherePlaylist($champ, $valeur, $table);
        }
        
        
        return $this->render("admin/admin.playlist.html.twig", [
            'playlists' => $playlist,            
            'valeur' => $valeur,
            'table' => $table
        ]);
    } 
    
    /**
     * @Route("/admin/edit/playlist/{id}", name="admin.playlist.edit")
     * @param Playlist $playlist
     * @param Request $request
     * @return Response
     */
    public function edit(Playlist $playlist, Request $request): Response{
        $formPlaylist = $this->createForm(PlaylistType::class, $playlist);
        
        $formations = $this->formationRepository->findAll();
        $formPlaylist->handleRequest($request);
        if($formPlaylist->isSubmitted() && $formPlaylist->isValid()){
            $this->playlistRepository->add($playlist, true);
            return $this->redirectToRoute('admin.playlist');
        }
        return $this->render("admin/admin.playlist.edit.html.twig", [
            'playlist' => $playlist,
            'formations'=>$formations,
            'formPlaylist' => $formPlaylist->createView()
        ]);
    }
    
    /**
     * @Route("/admin/ajout/playlist}", name="admin.playlist.ajout")     
     * @param Request $request
     * @return Response
     */
    public function ajout(Request $request): Response{
        $playlist = new Playlist();
        $formPlaylist = $this->createForm(PlaylistType::class, $playlist);
        
        $formPlaylist->handleRequest($request);       
            
        if($formPlaylist->isSubmitted() && $formPlaylist->isValid()){
            $this->playlistRepository->add($playlist, true);
            return $this->redirectToRoute('admin.playlist');
        }
           
        return $this->render("admin/admin.playlist.ajout.html.twig", [
            'playlist' => $playlist,
            'formPlaylist' => $formPlaylist->createView()
        ]); 
    }
}
