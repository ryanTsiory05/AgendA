<?php

namespace App\Controller;

use App\Entity\Calendar;
use App\Entity\Entretien;
use App\Form\CalendarType;
use App\Repository\CalendarRepository;
use App\Repository\CandidatRepository;
use App\Repository\EntretienRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/calendar")
 */
class CalendarController extends AbstractController
{
    /**
     * @Route("/", name="calendar_index", methods={"GET"})
     */
    public function index(CalendarRepository $calendarRepository, EntretienRepository $entretienRepository, CandidatRepository $candidatRepository): Response
    {

        $a = $calendarRepository->findAll();
        $b = $entretienRepository->findAll();
        $c = $candidatRepository->findAll();


        return $this->render('calendar/index.html.twig', [
            'calendars' => $a

        ]);
    }

    /**
     * @Route("/new", name="calendar_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager, candidatRepository $candidatRepository): Response
    {


        $calendar = new Calendar();
        $form = $this->createForm(CalendarType::class, $calendar);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($calendar);
            $entityManager->flush();

            return $this->redirectToRoute('calendar_index', [], Response::HTTP_SEE_OTHER);
        }

        return
            $this->renderForm('calendar/new.html.twig', [
                'calendar' => $calendar,
                'form' => $form

            ]);
    }





    /**
     * @Route("/{id}", name="calendar_show", methods={"GET"})
     */
    public function show(Calendar $calendar, EntityManagerInterface $entityManager, EntretienRepository $entretienRepository): Response
    {

        $entretien = $entretienRepository->findOneBy(array('id' => $calendar->getEntretien()));

        return $this->render('calendar/show.html.twig', [
            'calendar' => $calendar,
            'entretien' => $entretien
        ]);
    }

    /**
     * @Route("/{id}/edit", name="calendar_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Calendar $calendar, EntityManagerInterface $entityManager, Entretien $entretien = null): Response
    {
        $formEnt = $this->createFormBuilder($entretien);
        $formEnt
            ->add('date')
            ->add('heure')
            ->add('lieu')
            ->add('candidat');
        $formEnt = $formEnt->getForm();

        $formEnt->handleRequest($request);


        $form = $this->createForm(CalendarType::class, $calendar);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('calendar_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('calendar/edit.html.twig', [
            'calendar' => $calendar,
            'form' => $form,
            'formEnt' => $formEnt
        ]);
    }

    /**
     * @Route("/{id}", name="calendar_delete", methods={"POST"})
     */
    public function delete(Request $request, Calendar $calendar, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $calendar->getId(), $request->request->get('_token'))) {
            $entityManager->remove($calendar);
            $entityManager->flush();
        }

        return $this->redirectToRoute('calendar_index', [], Response::HTTP_SEE_OTHER);
    }
}
