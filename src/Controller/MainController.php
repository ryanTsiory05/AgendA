<?php

namespace App\Controller;

use DateTime;
use DateInterval;
use ArrayIterator;
use MultipleIterator;
use App\Entity\Calendar;
use App\Entity\Entretien;
use PhpParser\Node\Expr\Cast\Array_;
use App\Repository\CalendarRepository;
use App\Repository\CandidatRepository;
use App\Repository\EntretienRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MainController extends AbstractController
{
    /**
     * @Route("/calendrier", name="calendrier")
     */
    public function index(CalendarRepository $calendarRepository, EntretienRepository $entretienRepository, CandidatRepository $candidatRepository): Response
    {
        $events = $calendarRepository->findAll();
        $entretiens = $entretienRepository->findAll();
        $candidats = $candidatRepository->findAll();

        $rdvs = [];


        foreach ($events as $event) {
            $startdate = $event->getEntretien()->getDate()->format('Y-m-d');

            $startheure = $event->getEntretien()->getHeure()->format('H:i:s');



            $start =  $startdate . " " . $startheure;

            $end = $startdate . " " . $startheure;



            $end = new \DateTime($end);
            $end->add(new DateInterval('PT2H'));

            $end = $end->format('Y-m-d H:i:s');

            $rdvs[] = [
                'id' => $event->getId(),
                'backgroundColor' => $event->getBackgroundcolor(),
                'textColor' => $event->getTextcolor(),
                'entretien' => $event->getEntretien()->getId(),
                'start' => $start,
                'end' => $end,
                'description' => $event->getEntretien()->getLieu(),
                'candidat' => $event->getEntretien()->getCandidat()->getId(),
                'title' => "Entretien - " . $event->getEntretien()->getCandidat()->getNom() . " " . $event->getEntretien()->getLieu(),
                'prenom' => $event->getEntretien()->getCandidat()->getPrenom(),
                'email' => $event->getEntretien()->getCandidat()->getEmail()

            ];
        }
        $data = json_encode($rdvs);


        return $this->render('main/index.html.twig', compact('data'));
    }
}
