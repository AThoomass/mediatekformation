<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\NiveauRepository;
use App\Entity\Niveau;
use App\Form\NiveauType;

class AdminNiveauxController extends AbstractController
{
    private const PAGEFORMATIONS = "admin/admin.niveaux.html.twig";

    /**
     *
     * @var NiveauRepository
     */
    private $repository;
    
    /**
     *
     * @var EntityManagerInterface 
     */
    private $om;
    
    /**
     * 
     * @param NiveauRepository $repository
     */
    function __construct(NiveauRepository $repository, EntityManagerInterface $om) {
        $this->repository = $repository;
        $this->om = $om;
    }
    
    /**
     * @Route("/admin/niveaux", name="admin.niveaux")
     */
    public function index(): Response
    {
        $niveaux = $this->repository->findAll();
        return $this->render('admin/admin.niveaux.html.twig', [
            'niveaux' => $niveaux
        ]);
    }
    
    /**
     * @Route("/admin/niveau/delete/{id}", name="admin.niveau.delete")
     * @param Niveau $niveau
     * @return Response
     */
    public function delete(Niveau $niveau): Response{
        $formations = $niveau->getFormations();
        if($formations->isEmpty()){
            $this->om->remove($niveau);
            $this->om->flush();
        } else {
            $this->addFlash('erreur', 'Ce niveau est utilisé et ne peut être supprimé.');
        }
        return $this->redirectToRoute('admin.niveaux');
    }
    
    /**
     * @Route("/admin/niveau/ajout", name="admin.niveau.ajout")
     * @param Request $request
     * @return Response
     */
    public function ajout(Request $request): Response{
        if($this->isCsrfTokenValid('ajout_niveau', $request->get('_token'))){
            if( $request->get('libelle') != ""){
                $niveau = new Niveau();
                $niveau->setLibelle($request->get('libelle'));
                $this->om->persist($niveau);
                $this->om->flush();
            } else {
                $this->addFlash('erreur', 'Libellé incorrect');
            }
        }
        return $this->redirectToRoute('admin.niveaux');
    }
}
