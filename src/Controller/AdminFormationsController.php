<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\FormationRepository;
use App\Entity\Niveau;
use App\Entity\Formation;
use App\Form\FormationType;

class AdminFormationsController extends AbstractController
{
    private const PAGEFORMATIONS = "admin/admin.formations.html.twig";

    /**
     *
     * @var FormationRepository
     */
    private $repository;
    
    /**
     *
     * @var EntityManagerInterface 
     */
    private $om;
    
    /**
     * 
     * @param FormationRepository $repository
     */
    function __construct(FormationRepository $repository, EntityManagerInterface $om) {
        $this->repository = $repository;
        $this->om = $om;
    }
    
    /**
     * @Route("/admin/formations", name="admin.formations")
     */
    public function index(): Response
    {
        $formations = $this->repository->findAll();
        $niveaux = $this->getDoctrine()
            ->getRepository(Niveau::class)
            ->findAll();
        return $this->render('admin/admin.formations.html.twig', [
            'formations' => $formations,
            'niveaux' => $niveaux
        ]);
    }
    /**
     * @Route("/admin/formations/tri/{champ}/{ordre}", name="admin.formations.sort")
     * @param type $champ
     * @param type $ordre
     * @return Response
     */
    public function sort($champ, $ordre): Response{
        $formations = $this->repository->findAllOrderBy($champ, $ordre);
        $niveaux = $this->getDoctrine()
            ->getRepository(Niveau::class)
            ->findAll();
        return $this->render(self::PAGEFORMATIONS, [
           'formations' => $formations,
           'niveaux' => $niveaux
        ]);
    }   
        
    /**
     * @Route("/admin/formations/recherche/{champ}", name="admin.formations.findallcontain")
     * @param type $champ
     * @param Request $request
     * @return Response
     */
    public function findAllContain($champ, Request $request): Response{
        if($this->isCsrfTokenValid('filtre_'.$champ, $request->get('_token'))){
            $valeur = $request->get("recherche");
            dump($valeur);
            $formations = $this->repository->findByContainValue($champ, $valeur);
            
            $niveaux = $this->getDoctrine()
            ->getRepository(Niveau::class)
            ->findAll();
            return $this->render(self::PAGEFORMATIONS, [
               'formations' => $formations,
               'niveaux' => $niveaux
            ]);
        }
        return $this->redirectToRoute("admin.formations");
    }  

    /**
     * @Route("/admin/formations/filtre/{champ}", name="admin.formations.findbyniveau")
     * @param type $champ
     * @param Request $request
     * @return Response
     */
    public function findByNiveau($champ, Request $request): Response{
        if($this->isCsrfTokenValid('filtre_'.$champ, $request->get('_token')) &&  $request->get('id') != 0){
            $niveaux = $this->getDoctrine()
                ->getRepository(Niveau::class)
                ->findAll();
            $niveau = $this->getDoctrine()->getRepository(Niveau::class)->find($request->get('id'));
            $formations = $niveau->getFormations();
            return $this->render(self::PAGEFORMATIONS, [
               'formations' => $formations,
               'niveaux' => $niveaux
            ]);
        }
        return $this->redirectToRoute("admin.formations");
    } 
    
    /**
     * @Route("/admin/formation/ajout", name="admin.formation.ajout")
     * @param Request $request
     * @return Response
     */
    public function ajout(Request $request): Response{
        $formation = new Formation();
        $formFormation = $this->createForm(FormationType::class, $formation);
        
        $formFormation->handleRequest($request);
        if($formFormation->isSubmitted() && $formFormation->isValid()){
            $this->om->persist($formation);
            $this->om->flush();
            return $this->redirectToRoute('admin.formations');
        }
        
        return $this->render("admin/admin.formation.ajout.html.twig", [
           'formation' => $formation,
           'formformation' => $formFormation->createView()
        ]);
    }
    
    /**
     * @Route("/admin/formation/{id}", name="admin.formation.edit")
     * @param Formation $formation
     * @param Request $request
     * @return Response
     */
    public function edit(Formation $formation, Request $request): Response{
        $formFormation = $this->createForm(FormationType::class, $formation);
        
        $formFormation->handleRequest($request);
        if($formFormation->isSubmitted() && $formFormation->isValid()){
            $this->om->flush();
            return $this->redirectToRoute('admin.formations');
        }
        
        return $this->render("admin/admin.formation.edit.html.twig", [
           'formation' => $formation,
           'formformation' => $formFormation->createView()
        ]);
    }
    
    /**
     * @Route("/admin/formation/delete/{id}", name="admin.formation.delete")
     * @param Formation $formation
     * @return Response
     */
    public function delete(Formation $formation): Response{
        $this->om->remove($formation);
        $this->om->flush();
        return $this->redirectToRoute('admin.formations');
    }
}
