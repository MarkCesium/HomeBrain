<?php

namespace App\Controller;

use App\Entity\IconImage;
use App\Entity\Location;
use App\Entity\Publisher;
use App\Entity\PublisherDescription;
use App\Entity\PublisherSetting;
use App\Entity\UserLocation;
use App\Form\LocationDataType;
use App\Form\LocationType;
use App\Form\PublisherType;
use ErrorException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\VarDumper\VarDumper;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use function Symfony\Component\DomCrawler\Test\Constraint\toString;

//use Symfony\UX\Chartjs\Model\Chart;

class AppController extends AbstractController
{
    public function index(Request $request, EntityManagerInterface $em, UserInterface $user, ChartBuilderInterface $chartBuilder): Response
    {
//        $chart = $chartBuilder->createChart(Chart::TYPE_LINE);
//        $chart->setData([
//            'labels' => ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
//            'datasets' => [
//                [
//                    'label' => 'Random!',
//                    'backgroundColor' => '#6E9EF9',
//                    'borderColor' => '#6E9EF9',
//                    'data' => [522, 1500, 2250, 2197, 2345, 3122, 3099],
//                ],
//            ],
//        ]);
        $locations = $em->getRepository(Location::class)->findUserLocations($user->getId());
        $location = new Location();

        $form = $this->createForm(LocationType::class, $location);
        $formLocationData = $this->createForm(LocationDataType::class);

        return $this->render('app/index.html.twig', [
            'locations' => array_reverse($locations),
            'form' => $form,
            'formLocationData' => $formLocationData
        ]);
    }

    /**
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param SluggerInterface $slugger
     * @param UserInterface $user
     * @return Response
     */
    public function addLocation(
        Request $request,
        EntityManagerInterface $em,
        SluggerInterface $slugger,
        UserInterface $user
    ): Response
    {
        $location = new Location();
        $form = $this->createForm(LocationType::class, $location);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $icon = $form->get('icon')->getData();
            VarDumper::dump($form);
            if ((!$icon && !$location->getIconImage()) || ($location->getIconImage() && $icon)) {
                $this->addFlash('danger', 'Something went wrong!');
                return $this->redirectToRoute('index');
            }
            if ($icon) {
                $originalFilename = pathinfo($icon->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $path = sprintf('/img/locations/%s/', $user->getId());
                $newFilename = sprintf('%s-%s.%s', substr($safeFilename, 0, 150), uniqid(), $icon->guessExtension());
                $filePath = sprintf('%s/public%s', $this->getParameter('kernel.project_dir'), $path);
                $icon->move(
                    $filePath,
                    $newFilename
                );
                $location->setIcon(sprintf('%s%s', $path, $newFilename));
            }
            try {
                $em->persist($location);
                $em->flush();
                $userLocation = new UserLocation();
                $userLocation->setUser($user);
                $userLocation->setLocation($location);
                $em->persist($userLocation);
                $em->flush();
                $this->addFlash('success', 'Room was successful added!');
            } catch (ErrorException) {
                $this->addFlash('danger', 'Something went wrong!');
            }
        }

        return $this->redirectToRoute('index');
    }

    /**
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param SluggerInterface $slugger
     * @param UserInterface $user
     * @return Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function changeLocation(
        Request $request,
        EntityManagerInterface $em,
        SluggerInterface $slugger,
        UserInterface $user
    ): Response
    {
        $data = $request->request->all()['location_data'];
        $locationId = $data['id'];
//        VarDumper::dump($locationId);
        $location = $em->getRepository(Location::class)->findUserLocation($locationId, $user->getId());
//        VarDumper::dump($location);
        $form = $this->createForm(LocationDataType::class, $location);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $icon = $form->get('icon')->getData();
            $iconImage = $form->get('icon_image')->getData();
//            VarDumper::dump($form);
            if ((!$icon && !$location->getIconImage()) || ($location->getIconImage() && $icon)) {
                $this->addFlash('danger', 'Something went wrong!');

                return $this->redirectToRoute('index');
            }
            if ($icon) {
                $originalFilename = pathinfo($icon->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $path = sprintf('/img/locations/%s/', $user->getId());
                $newFilename = sprintf('%s-%s.%s', substr($safeFilename, 0, 150), uniqid(), $icon->guessExtension());
                $filePath = sprintf('%s/public%s', $this->getParameter('kernel.project_dir'), $path);
                $icon->move(
                    $filePath,
                    $newFilename
                );
                $location->setIcon(sprintf('%s%s', $path, $newFilename));
            }
            elseif ($iconImage) {
                $location->setIconImage($em->getRepository(IconImage::class)->findOneBy(['id' => $iconImage->getId()]));
                if ($location->getIcon()) {
                    $location->setIcon(null);
                }
            }
            try {
                $em->persist($location);
                $em->flush();
                $this->addFlash('success', 'Room was successful edited!');
            } catch (ErrorException) {
                $this->addFlash('danger', 'Something went wrong!2');
            }
        }

        return $this->redirectToRoute('index');
    }

    public function dashboard()
    {

        return $this->render('app/dashboard.html.twig');
    }

    /**
     * @param UserInterface $user
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function location
    (
        UserInterface $user,
        EntityManagerInterface $em
    ): Response
    {
        $locations = $em->getRepository(Location::class)->findUserLocations($user->getId());
        $publisher = new Publisher();
        $form = $this->createForm(PublisherType::class, $publisher);
        return $this->render('app/location.html.twig', [
            'locations' => array_reverse($locations),
            'form' => $form,
        ]);
    }

    /**
     * @param Location $location
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getLocation
    (
        Location $location
    )
    {
        $data = [
            'id' => $location->getId(),
            'name' => $location->getName()
        ];
        if ($location->getIconImage()) {
            $data['iconImage'] = $location->getIconImage()->getId();
        }

        return $this->json($data);
    }

    /**
     * @param Location $location
     * @param EntityManagerInterface $em
     * @param UserInterface $user
     * @return Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getPublishers
    (
        Location $location,
        EntityManagerInterface $em,
        UserInterface $user
    ): Response
    {
        $response = [
            'data' => [
                'name' => '',
                'devices' => [],
                'sensors' => []
            ],
            'status' => 1
        ];
        if (!$em->getRepository(Location::class)->findUserLocation($location->getId(), $user->getId())) {
            $response['data']['name'] = 'Unavailable';
            $response['status'] = 0;

            return $this->json($response);
        }
        $response['data']['name'] = $location->getName();
        $publisher = $em->getRepository(Location::class)->findUserPublishers($location->getId(), $user->getId());
        foreach ($publisher as $item) {
            if ($item->getType() == 1) {
                $response['data']['devices'][] = $item->getAsArray();
            } else {
                $response['data']['sensors'][] = $item->getAsArray();
            }
        }

        return $this->json($response);
    }

    /**
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param SluggerInterface $slugger
     * @param UserInterface $user
     * @return Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function addPublisher(
        Request $request,
        EntityManagerInterface $em,
        SluggerInterface $slugger,
        UserInterface $user
    ): Response
    {
        $publisher = new Publisher();
        $form = $this->createForm(PublisherType::class, $publisher);
        $locationId = $request->get('publisher')['location'];
        $location = $em->getRepository(Location::class)->findUserLocation($locationId, $user->getId());
        if (!$location) {
            $this->addFlash('danger', 'Publisher is not added! Something went wrong');
            return $this->redirectToRoute('index');
        }
        $publisher->setLocation($location);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $em->persist($publisher);
                $em->flush();
                $this->addFlash('success', 'Publisher was successful added!');
            } catch (ErrorException) {
                $this->addFlash('danger', 'Something went wrong!');
            }

        }

        # redirect to room !!!!!

        return $this->redirectToRoute('app_location', [
            '_fragment' => $locationId
        ]);
    }

    public function arduinoImitation(): Response
    {
        return $this->render('trash.html.twig');
    }
}
