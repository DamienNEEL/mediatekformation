<?php

namespace App\Repository;

use App\Entity\Playlist;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;


const PLAYLIST_ALIAS = "p";
const ID = "id";
const NAME = "name";
const FORMATION = "formation";
const FORMATIONS = "formations";
const CATEGORIE = "categorie";
const CATEGORIES = "categories";
const CATEGORIE_ALIAS = "c";
const FORMATION_ALIAS = "f";
const TITLE = "title";
/**
 * @extends ServiceEntityRepository<Playlist>
 *
 * @method Playlist|null find($id, $lockMode = null, $lockVersion = null)
 * @method Playlist|null findOneBy(array $criteria, array $orderBy = null)
 * @method Playlist[]    findAll()
 * @method Playlist[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlaylistRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Playlist::class);
    }

    public function add(Playlist $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Playlist $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
    
    /**
     * Retourne toutes les playlists triées sur le nom de la playlist    
     * @param type $ordre
     * @return Playlist[]
     */
    public function findAllOrderByName($ordre): array{
        return $this->createQueryBuilder(PLAYLIST_ALIAS)               
                ->leftjoin(PLAYLIST_ALIAS.".".FORMATIONS, FORMATION_ALIAS)                
                ->groupBy(PLAYLIST_ALIAS.".".ID)
                ->orderBy(PLAYLIST_ALIAS.".".NAME, $ordre)               
                ->getQuery()
                ->getResult();       
    }
    
    /**
     * Retourne toutes les playlists triées sur le nombre de formations     
     * @param type $ordre
     * @return Playlist[]
     */
    public function findAllOrderByNbFormations($ordre): array{
        return $this->createQueryBuilder(PLAYLIST_ALIAS)                
                ->leftjoin(PLAYLIST_ALIAS.".".FORMATIONS, FORMATION_ALIAS)               
                ->groupBy(PLAYLIST_ALIAS.".".ID)               
                ->orderBy('count('.FORMATION_ALIAS.".".TITLE.')', $ordre)                
                ->getQuery()
                ->getResult();       
    }

    /**
     * Enregistrements dont un champ contient une valeur
     * ou tous les enregistrements si la valeur est vide
     * @param type $champ
     * @param type $valeur
     * @return Playlist[]
     */
    public function findByContainValueWherePlaylist($champ, $valeur): array{
        if($valeur==""){
            return $this->findAllOrderByName('ASC');          
        }
        return $this->createQueryBuilder(PLAYLIST_ALIAS)                    
                    ->leftjoin(PLAYLIST_ALIAS.".".FORMATIONS, FORMATION_ALIAS)                    
                    ->where(PLAYLIST_ALIAS.".".$champ.' LIKE :valeur')
                    ->setParameter('valeur', '%'.$valeur.'%')
                    ->groupBy(PLAYLIST_ALIAS.".".ID)                   
                    ->orderBy(PLAYLIST_ALIAS.".".NAME, 'ASC')                  
                    ->getQuery()
                    ->getResult();   
    }
    
    /**
     * Enregistrements dont un champ contient une valeur
     * ou tous les enregistrements si la valeur est vide
     * @param type $champ
     * @param type $valeur
     * @param type $table si $champ dans une autre table
     * @return Playlist[]
     */
    public function findByContainValueWhereCategorie($champ, $valeur): array{
        if($valeur==""){
            return $this->findAllOrderByName('ASC');
        }    
        return $this->createQueryBuilder(PLAYLIST_ALIAS)                
                ->leftjoin(PLAYLIST_ALIAS.".".FORMATIONS, FORMATION_ALIAS)
                ->leftjoin(FORMATION_ALIAS.".".CATEGORIES, CATEGORIE_ALIAS)
                ->where(CATEGORIE_ALIAS.".".$champ.' LIKE :valeur')
                ->setParameter('valeur', '%'.$valeur.'%')
                ->groupBy(PLAYLIST_ALIAS.".".ID)               
                ->orderBy(PLAYLIST_ALIAS.".".NAME, 'ASC')               
                ->getQuery()
                ->getResult();
    }    


    
}
