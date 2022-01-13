<?php

namespace App\Controller;


use App\Entity\Candidat;
use App\Entity\Entretien;
use App\Repository\CandidatRepository;
use App\Repository\EntretienRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AgendaController extends AbstractController
{

    /**
     * @Route("/", name="agenda")
     */
    public function index(EntretienRepository $entretienRepository, $date = null): Response
    {
        $entretien = $entretienRepository->findByDateAsc($date);


        return $this->render('agenda/index.html.twig', [
            'controller_name' => 'AgendaController',
            'entretien' => $entretien
        ]);
    }


    /**
     * @Route("/ajout_entretien_candidat", name="ajout_entretien_candidat")
     */
    public function ajouterEntretienCandidat(Request $request, EntityManagerInterface $manager, CandidatRepository $candidatRepository)
    {
        $candidat = new Candidat();
        $formCandidat = $this->createFormBuilder($candidat);
        $formCandidat
            ->add('nom')
            ->add('prenom')
            ->add('email');
        $formCandidat = $formCandidat->getForm();

        $formCandidat->handleRequest($request);

        if ($formCandidat->isSubmitted() && $formCandidat->isValid()) {
            $manager->persist($candidat);
            $manager->flush();

            $id = $candidat->getId();


            return $this->redirectToRoute('ajout_entretien_c', [
                'id' => $id
            ]);
        }
        return $this->render('agenda/ajout_entretien_candidat.html.twig', [
            'formCandidat' => $formCandidat->createView(),

        ]);
    }


    /**
     * @Route("/ajout_entretien", name="ajout_entretien")
     */
    public function ajoutEntretien(Request $request, EntityManagerInterface $manager)
    {
        $entretien = new Entretien();
        $form = $this->createFormBuilder($entretien);
        $form
            ->add('date')
            ->add('heure')
            ->add('lieu')
            ->add('candidat');
        $form = $form->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($entretien);
            $manager->flush();
            return $this->redirectToRoute('calendar_new');
        }
        return $this->render('agenda/ajout_entretien.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/ajout_entretien_c/{id}", name="ajout_entretien_c")
     */
    public function ajoutEntretienC(Request $request, EntityManagerInterface $manager, CandidatRepository $candidatRepository, $id)
    {
        $candidat = $candidatRepository->findOneBy(array('id' => $id));

        $entretien = new Entretien();
        $entretien->setCandidat($candidat);
        $form = $this->createFormBuilder($entretien);
        $form
            ->add('date')
            ->add('heure')
            ->add('lieu')
            ->add('candidat');
        $form = $form->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entretien->setCandidat($candidat);

            $manager->persist($entretien);
            $manager->flush();
            return $this->redirectToRoute('calendar_new');
        }
        return $this->render('agenda/ajout_entretienC.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="modif_entretien")
     */
    public function modfiEntretien(Request $request, EntityManagerInterface $manager, Entretien $entretien = null)
    {
        $form = $this->createFormBuilder($entretien);
        $form
            ->add('date')
            ->add('heure')
            ->add('lieu')
            ->add('candidat');
        $form = $form->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($entretien);
            $manager->flush();

            return $this->redirectToRoute('agenda');
        }
        return $this->render('agenda/modifier_entretien.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/{id}/supp", name="supp_entretien")
     */
    public function suppEntretien(EntityManagerInterface $manager, EntretienRepository $entretienRepository, $id)
    {
        $entretien = $entretienRepository->findOneBy(array('id' => $id));

        $manager->remove($entretien);
        $manager->flush();

        return $this->redirectToRoute('agenda');
    }



    /**
     * @Route("/liste_candidat", name="liste_candidat")
     */
    public function listeCandidat(CandidatRepository $candidatRepository): Response
    {

        $candidats = $candidatRepository->findAll();

        return $this->render('agenda/listeCandidat.html.twig', [
            'candidats' => $candidats
        ]);
    }

    /**
     * @Route("/liste_entretien_candidat/{id}", name="liste_entretien_candidat")
     */
    public function listeEntretiensCandidat(EntretienRepository $entretienRepository, CandidatRepository $candidatRepository, $id): Response
    {


        $entretiens = $entretienRepository->findBy(array('candidat' => $id));

        return $this->render('agenda/listeEntretienCandidat.html.twig', [
            'entretiens' => $entretiens
        ]);
    }
}
