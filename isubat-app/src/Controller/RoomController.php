<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Entity\Room;
use App\Form\RoomType;
use App\Repository\ReservationRepository;
use App\Repository\RoomRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class RoomController
 * @package App\Controller
 * @Route("/room",name="room.")
 */
class RoomController extends AbstractController
{
    /**
     * @Route("/", name="show")
     * @param RoomRepository $roomRepository
     * @return RedirectResponse|Response
     */
    public function index(RoomRepository $roomRepository, Request $request)
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->redirect('/login');
        }
        $room = new Room();
        $form = $this->createForm(RoomType::class, $room);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($room);
            $em->flush();
            $this->addFlash('success', 'Room was created');
            unset($room);
            unset($form);
            $room = new Room();
            $form = $this->createForm(RoomType::class, $room);
        }
        $posts = $roomRepository->findAll();

        return $this->render('room/index.html.twig', [
            'posts' => $posts,
            'form' => $form->createView(),
            'login' => $user->getLogin()
        ]);
    }

    /**
     * @Route("/create", name="create", methods={"POST"})
     */
    public function create(Request $request)
    {
//        $request->get
        $room = new Room();

        $room->setNumber(1);
        $em = $this->getDoctrine()->getManager();
        $em->persist($room);
        $em->flush();
        return new Response("test");
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete($id, RoomRepository $repository)
    {
//        $request->get
        $room = $repository->find($id);
        if ($room) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($room);
            $em->flush();
            $this->addFlash('success', "Room was deleted");
        }


        return $this->redirect($this->generateUrl(('room.show')));
    }



    /**
     * @Route("/list", name="list")
     * @param RoomRepository $roomRepository
     * @return RedirectResponse|Response
     */
    public function list(RoomRepository $roomRepository, Request $request)
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->redirect('/login');
        }

        $posts = $roomRepository->findAll();

        return $this->render('room/list.html.twig', [
            'posts' => $posts,
            'login' => $user->getLogin()
        ]);
    }


    /**
     * @Route("/deleteReservation/{id}/{reservationId?}", name="deleteReservation")
     */
    public function deleteReservation($id, ReservationRepository $repository,RoomRepository $roomRepository,$reservationId)
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirect('/login');
        }
        $room = $roomRepository->find($id);

        $reserv = $repository->find($reservationId);
        $date = $reserv->getDate();
        if ($reserv) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($reserv);
            $em->flush();
            $this->addFlash('success', "Reservation was deleted");
        }

        $reservations = $repository->findBy(['roomId' => $id, 'date' => $date]);
        $timeArray = $this->getTimeArray($reservations);
        return $this->render('room/reserve.html.twig', [
            'room' => $room,
            'reservations' => $reservations,
            'times' => $timeArray,
            'login' => $user->getLogin(),
            'day' => $date
        ]);

    }
    /**
     * @Route("/reserve/{id}", name="reserve")
     * @Route("/reserve/{id}/{date}", name="reserve")
     * @param RoomRepository $roomRepository
     * @return RedirectResponse|Response
     */
    public function reserve($id, RoomRepository $roomRepository, ReservationRepository $reservationRepository, Request $request, $date = null)
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirect('/login');
        }
        $room = $roomRepository->find($id);
        if ($date) {
            if ($request->query->get('operation') == "plus") {
                $day = date('Y-m-d', strtotime($date . ' +1 day'));
            } else {
                $day = date('Y-m-d', strtotime($date . ' -1 day'));
            }
        } else {
            $day = date("Y-m-d");
        }

        $reservations = $reservationRepository->findBy(['roomId' => $id, 'date' => $day]);
        $timeArray = $this->getTimeArray($reservations);

        return $this->render('room/reserve.html.twig', [
            'room' => $room,
            'reservations' => $reservations,
            'times' => $timeArray,
            'login' => $user->getLogin(),
            'day' => $day
        ]);
    }

    /**
     * @Route("/reserveRoom/{id}", name="reserveRoom")
     * @param $id
     * @param ReservationRepository $repository
     * @return RedirectResponse|Response
     */
    public function reserveRoom($id, ReservationRepository $repository,RoomRepository $roomRepository, Request $request)
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirect('/login');
        }
        $room = $roomRepository->find($id);

        $time = $request->query->get('time');
        $date = $request->query->get('date');

        $reserv = $repository->findBy(['roomId' => $id, 'date' => $date,'time'=>$time]);
        if(!$reserv) {
            $em = $this->getDoctrine()->getManager();
            $reservation = new Reservation();
            $reservation->setDate($date);
            $reservation->setTime($time);
            $reservation->setUser($user->getLogin());
            $reservation->setRoomId($id);
            $reservation->setUserId($user->getId());
            $em->persist($reservation);
            $em->flush();
        }

        $this->addFlash('success', "Reservation complete");

        $reservations = $repository->findBy(['roomId' => $id, 'date' => $date]);
        $timeArray = $this->getTimeArray($reservations);

        return $this->render('room/reserve.html.twig', [
            'room' => $room,
            'times' => $timeArray,
            'reservations' => $reservations,
            'day' => $date,
            'login' => $user->getLogin()
        ]);
    }

    private function getTimeArray($reservation): array
    {
        $times = ["8:00", "9:30", "11:00", "12:30", "14:00", "15:30", "17:00", "18:30"];
        foreach ($reservation as $reserv) {
            if (($key = array_search($reserv->getTime(), $times)) !== false) {
                unset($times[$key]);
            }
        }
        return $times;
    }

}
