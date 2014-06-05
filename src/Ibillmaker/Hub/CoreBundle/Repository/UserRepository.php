<?php
namespace Ibillmaker\Hub\CoreBundle\Repository;

use Sylius\Bundle\CoreBundle\Repository\UserRepository as BaseUserRepository;
use Symfony\Component\Security\Core\SecurityContextInterface;
class UserRepository extends BaseUserRepository 
{
    
    
    protected $user;

    public function setUserViaSecurityContext(SecurityContextInterface $securityContext)
    {
        $this->user = $securityContext->getToken()->getUser();
    }
    
    /**
     * Create filter paginator.
     *
     * @param array $criteria
     * @param array $sorting
     *
     * @return PagerfantaInterface
     */
    public function createFilterPaginator($criteria = array(), $sorting = array(), $deleted = false)
    {   
        $queryBuilder = parent::getCollectionQueryBuilder();

        if ($deleted) {
            $this->_em->getFilters()->disable('softdeleteable');
        }
        $adminUser = $this->user->getAdmin();
        
         
        
        
   
        if (isset($criteria['query'])) {
            $queryBuilder
                ->where('o.username LIKE :query')
                ->orWhere('o.email LIKE :query')
                ->innerJoin('o.admin', 'admin')
                ->andWhere('admin = :admin')
                ->setParameter('admin', $this->user)    
                ->setParameter('query', '%'.$criteria['query'].'%')
                
            ;
        }
        else
        {
            if($adminUser=$this->user->getAdmin() !== NULL && !isset($criteria)){
          $queryBuilder
            ->innerJoin('o.admin', 'admin')
            ->andWhere('admin = :admin')
            ->setParameter('admin', $adminUser)
        ;
        }else{
           $queryBuilder
            ->innerJoin('o.admin', 'admin')
            ->andWhere('admin = :admin')
            ->setParameter('admin', $this->user);
        }
        }
        
        if (isset($criteria['enabled'])) {
            $queryBuilder
                ->andWhere('o.enabled = :enabled')
                ->setParameter('enabled', $criteria['enabled'])
            ;
        }

        if (empty($sorting)) {
            if (!is_array($sorting)) {
                $sorting = array();
            }
            $sorting['updatedAt'] = 'desc';
        }

       // $this->applySorting($queryBuilder, $sorting);

        return $this->getPaginator($queryBuilder);
    }

    /**
     * Get the user data for the details page.
     *
     * @param integer $id
     */
    public function findForDetailsPage($id)
    {
        $queryBuilder = $this->getQueryBuilder();

        $this->_em->getFilters()->disable('softdeleteable');

        $queryBuilder
            ->andWhere($queryBuilder->expr()->eq('o.id', ':id'))
            ->setParameter('id', $id)
        ;

        $result = $queryBuilder
            ->getQuery()
            ->getOneOrNullResult()
        ;

        return $result;
    }

    public function countBetweenDates(\DateTime $from, \DateTime $to, $status = null)
    {
        $queryBuilder = $this->getCollectionQueryBuilderBetweenDates($from, $to);
        if (null !== $status) {
            $queryBuilder
                ->andWhere('o.status = :status')
                ->setParameter('status', $status)
            ;
        }

        return $queryBuilder
            ->select('count(o.id)')
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    protected function getCollectionQueryBuilderBetweenDates(\DateTime $from, \DateTime $to)
    {
        $queryBuilder = $this->getCollectionQueryBuilder();

        return $queryBuilder
            ->andWhere($queryBuilder->expr()->gte('o.createdAt', ':from'))
            ->andWhere($queryBuilder->expr()->lte('o.createdAt', ':to'))
            ->setParameter('from', $from)
            ->setParameter('to', $to)
        ;
    }
}


