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
     * Retourne toutes les playlists triées sur un champ
     * @param type $champ
     * @param type $ordre
     * @return Playlist[]
     */
    public function findAllOrderBy($champ, $ordre): array{
        return $this->createQueryBuilder(PLAYLIST_ALIAS)
                ->select(PLAYLIST_ALIAS.".".ID." ".ID)
                ->addSelect(PLAYLIST_ALIAS.".".NAME." ".NAME)
                ->addSelect(CATEGORIE_ALIAS.".".NAME." ".CATEGORIE.NAME)
                ->leftjoin(PLAYLIST_ALIAS.".".FORMATIONS, FORMATION_ALIAS)
                ->leftjoin(FORMATION_ALIAS.".".CATEGORIES, CATEGORIE_ALIAS)
                ->groupBy(PLAYLIST_ALIAS.".".ID)
                ->addGroupBy(CATEGORIE_ALIAS.".".NAME)
                ->orderBy(PLAYLIST_ALIAS.".".$champ, $ordre)
                ->addOrderBy(CATEGORIE_ALIAS.".".NAME)
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
    public function findByContainValue($champ, $valeur, $table=""): array{
        if($valeur==""){
            return $this->findAllOrderBy(NAME, 'ASC');
        }    
        if($table==""){      
            return $this->createQueryBuilder(PLAYLIST_ALIAS)
                    ->select(PLAYLIST_ALIAS.".".ID." ".ID)
                    ->addSelect(PLAYLIST_ALIAS.".".NAME." ".NAME)
                    ->addSelect(CATEGORIE_ALIAS.".".NAME." ".CATEGORIE.NAME)
                    ->leftjoin(PLAYLIST_ALIAS.".".FORMATIONS, FORMATION_ALIAS)
                    ->leftjoin(FORMATION_ALIAS.".".CATEGORIES, CATEGORIE_ALIAS)
                    ->where(PLAYLIST_ALIAS.".".$champ.' LIKE :valeur')
                    ->setParameter('valeur', '%'.$valeur.'%')
                    ->groupBy(PLAYLIST_ALIAS.".".ID)
                    ->addGroupBy(CATEGORIE_ALIAS.".".NAME)
                    ->orderBy(PLAYLIST_ALIAS.".".NAME, 'ASC')
                    ->addOrderBy(CATEGORIE_ALIAS.".".NAME)
                    ->getQuery()
                    ->getResult();              
        }else{   
            return $this->createQueryBuilder('p')
                    ->select(PLAYLIST_ALIAS.".".ID." ".ID)
                    ->addSelect(PLAYLIST_ALIAS.".".NAME." ".NAME)
                    ->addSelect(CATEGORIE_ALIAS.".".NAME." ".CATEGORIE.NAME)
                    ->leftjoin(PLAYLIST_ALIAS.".".FORMATIONS, FORMATION_ALIAS)
                    ->leftjoin(FORMATION_ALIAS.".".CATEGORIES, CATEGORIE_ALIAS)
                    ->where(CATEGORIE_ALIAS.".".$champ.' LIKE :valeur')
                    ->setParameter('valeur', '%'.$valeur.'%')
                    ->groupBy(PLAYLIST_ALIAS.".".ID)
                    ->addGroupBy(CATEGORIE_ALIAS.".".NAME)
                    ->orderBy(PLAYLIST_ALIAS.".".NAME, 'ASC')
                    ->addOrderBy(CATEGORIE_ALIAS.".".NAME)
                    ->getQuery()
                    ->getResult();              
            
        }           
    }    


    
}
