<?php

namespace App\Controller;

use App\Entity\IconImage;
use App\Entity\Location;
use App\Entity\Notice;
use App\Entity\Publisher;
use App\Entity\PublisherValueArchieve;
use App\Entity\User;
use App\Entity\UserApi;
use App\Entity\UserLocation;
use App\Form\PublisherDataType;
use App\Form\PublisherType;
use App\Form\UserApiEditType;
use ErrorException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\VarDumper\VarDumper;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class AppController extends AbstractController
{
    /**
     * @param EntityManagerInterface $em
     * @param UserInterface $user
     * @param ChartBuilderInterface $chartBuilder
     * @return Response
     */
    public function index
    (
        EntityManagerInterface $em,
        UserInterface $user,
        ChartBuilderInterface $chartBuilder
    ): Response
    {
        $locations = $em->getRepository(Location::class)->findUserLocations($user->getId());
        $plates = $em->getRepository(User::class)->fingUserPlates($user->getId());
        $choicePlates = [];
        foreach ($plates as $item) {
            $choicePlates[] = ['id' => $item->getId(), 'name' => $item->getUsername()];
        }
        $iconImages = $em->getRepository(IconImage::class)->findAll();
        $choiceIconImages = [];
        foreach ($iconImages as $item) {
            $choiceIconImages[] = ['id' => $item->getId(), 'name' => $item->getTitle()];
        }
        return $this->render('app/index.html.twig', [
            'locations' => array_reverse($locations),
            'plates' => $choicePlates,
            'icon_images' => $choiceIconImages
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
        $userApi = $em->getRepository(UserApi::class)->findOneBy(['id' => $request->request->all()['location']['user_api']]);
        $location->setName($request->request->all()['location']['name']);
        if ($request->files->all()['location']['icon']) {
            $icon = $request->files->all()['location']['icon'];
            if (filesize($icon) > 512000) {
                $this->addFlash('danger', 'Icon size is larger than allowed');
                return $this->redirectToRoute('index');
            }
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
            $location->setIconImage(null);
        } else if ($request->request->all()['location']['icon_image']) {
            $location->setIcon(null);
            $location->setIconImage($em->getRepository(IconImage::class)->findOneBy(['id' => $request->request->all()['location']['icon_image']]));
        } else {
            $this->addFlash('danger', 'Set your own location image or select our image');
            return $this->redirectToRoute('index');
        }
        $userApi->addLocation($location);
        $location->setUserApi($userApi);
        try {
            $em->persist($location);
            $em->flush();
            $userLocation = new UserLocation();
            $userLocation->setUser($user);
            $userLocation->setLocation($location);
            $em->persist($userLocation);
            $notice = new Notice(
                [
                    'location' => $location,
                    'action' => 2,
                    'text' => sprintf(
                        'Location "%s" was added',
                        $location->getName()
                    ),
                    'time' => (new \DateTime())->setTimestamp(time())
                ]
            );
            $em->persist($notice);
            $em->flush();
            $this->addFlash('success', 'Location was successful added!');
        } catch (ErrorException) {
            $this->addFlash('danger', 'Something went wrong!');
        }

        return $this->redirectToRoute('index');
    }

    /**
     * @param Location $location
     * @param EntityManagerInterface $em
     * @param UserInterface $user
     * @return Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function editLocation
    (
        Location $location,
        EntityManagerInterface $em,
        UserInterface $user
    ): Response
    {
        $data = [];
        $data['id'] = $location->getId();
        $data['devices'] = [];
        $data['sensors'] = [];
        if (!$em->getRepository(Location::class)->findUserLocation($data['id'], $user->getId())) {
            $this->addFlash('danger', 'You do not have access to this location!');

            return $this->redirectToRoute('index');
        }
        $data['name'] = $location->getName();
        $publisher = $em->getRepository(Location::class)->getLocationPublishers($location->getId());
        foreach ($publisher as $item) {
            if ($item->getType() == 1) {
                $data['devices'][] = $item->getAsArrayClient();
            } else {
                $data['sensors'][] = $item->getAsArrayClient();
            }
        }
        $plates = $em->getRepository(User::class)->fingUserPlates($user->getId());
        $choicePlates = [];
        $selectedPlate = $location->getUserApi();
        foreach ($plates as $item) {
            $choicePlates[] = ['id' => $item->getId(), 'name' => $item->getUsername(), 'selected' => ($item === $selectedPlate)];
        }
        $data['plates'] = $choicePlates;
        $iconImages = $em->getRepository(IconImage::class)->findAll();
        $choiceIconImages = [];
        $selectedIconImage = $location->getIconImage();
        foreach ($iconImages as $item) {
            $choiceIconImages[] = ['id' => $item->getId(), 'name' => $item->getTitle(), 'selected' => ($item === $selectedIconImage)];
        }
        $data['icon_images'] = $choiceIconImages;

        return $this->render(
            'app/edit_location.html.twig',
            $data
        );
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
        $name = $request->request->all()['location_data']['name'];
        $locationId = $request->request->all()['location_data']['id'];
        $location = $em->getRepository(Location::class)->findUserLocation($locationId, $user->getId());
        $userApi = $em->getRepository(UserApi::class)->findOneBy(['id' => $request->request->all()['location_data']['user_api']]);

        if ($request->files->all()['location_data']['icon']) {
            $icon = $request->files->all()['location_data']['icon'];
            if (filesize($icon) > 512000) {
                $this->addFlash('danger', 'Icon size is larger than allowed');
                return $this->redirectToRoute('index');
            }
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
            $location->setIconImage(null);
        } else if ($request->request->all()['location_data']['icon_image']) {
            $location->setIcon(null);
            $location->setIconImage($em->getRepository(IconImage::class)->findOneBy(['id' => $request->request->all()['location_data']['icon_image']]));
        }
        $userApi->addLocation($location);
        $location->setUserApi($userApi);
        $location->setName($name);
        try {
            $em->persist($location);
            $em->flush();
            $this->addFlash('success', 'Location has been successfully edited!');
        } catch (ErrorException) {
            $this->addFlash('danger', 'Something went wrong!');
        }

        return $this->redirectToRoute('index');
    }

    /**
     * @param Location $location
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param UserInterface $user
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function deleteLocation
    (
        Location $location,
        Request $request,
        EntityManagerInterface $em,
        UserInterface $user
    )
    {
        if (!$em->getRepository(Location::class)->findUserLocation($location->getId(), $user->getId())) {
            $this->addFlash('danger', 'You do not have access to this location!');

            return $this->redirectToRoute('index');
        }
        try {
            $name = $location->getName();
            $em->remove($location);
            $em->flush();
            $this->addFlash('success', sprintf('Room "%s" was deleted', $name));
        } catch (ErrorException) {
            $this->addFlash('danger', 'Something went wrong!');
        }


        return $this->redirectToRoute('index');
    }

    /**
     * @param EntityManagerInterface $em
     * @param UserInterface $user
     * @return Response
     */
    public function dashboard
    (
        EntityManagerInterface $em,
        UserInterface $user
    ): Response
    {
        $locationsData = [];
        $locations = $em->getRepository(Location::class)->findUserLocations($user->getId());
        foreach ($locations as $location) {
            $locationData = [
                'name' => $location->getName(),
                'chartDatas' => []
            ];
            $publishers = $em->getRepository(Location::class)->getLocationPublishers($location->getid());
            if ($publishers) {
                $currentTime = (new \DateTime())->setTimestamp(time());
                foreach ($publishers as $publisher) {
                    if ($publisher->getResponseType() === 'bool' || $publisher->getType() === 1) {
                        continue;
                    }
                    $publisherValuesArchieve = $em->getRepository(
                        PublisherValueArchieve::class)->findBy(['publisher' => $publisher],
                        ['updated' => 'DESC'],
                        limit: 480,
                    );
                    if (!$publisherValuesArchieve) {
                        continue;
                    }
                    $publisherValues = [];
                    $publisherLabels = [];
                    $counter = 0;
                    $max = 0;
                    foreach ($publisherValuesArchieve as $publisherValue) {
                        $updated = $publisherValue->getUpdated();
                        if ($currentTime->diff($updated)->h === $counter and $max < 12) {
                            $publisherValues[] = $publisherValue->getValue();
                            $publisherLabels[] = $publisherValue->getUpdated()->format('H:i');
                            $counter++;
                            $max++;
                        }
                    }
                    if (!$publisherValues) {
                        continue;
                    }
                    $locationData['chartDatas'][] = [
                        'name' => $publisher->getName(),
                        'id' => sprintf('chart_%s', $publisher->getId()),
                        'data' => [
                            'values' => array_reverse($publisherValues),
                            'labels' => array_reverse($publisherLabels)
                        ]
                    ];
                }
            }

            $locationsData[] = $locationData;
        }

        return $this->render('app/dashboard.html.twig', [
            'locationsData' => array_reverse($locationsData)
        ]);
    }

    /**
     * @param EntityManagerInterface $em
     * @param UserInterface $user
     * @return Response
     */
    public function notices
    (
        EntityManagerInterface $em,
        UserInterface $user
    ): Response
    {
        $notices = $em->getRepository(Notice::class)->findUserNotices($user->getId());
        return $this->render('app/notices.html.twig', [
            'notices' => array_reverse($notices)
        ]);
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
        Location $location,
        EntityManagerInterface $em
    )
    {
        $data = [
            'id' => $location->getId(),
            'name' => $location->getName()
        ];

        foreach ($em->getRepository(IconImage::class)->findAll() as $item) {
            $data['iconImages'][$item->getTitle()] = $item->getId();
        }
        if ($location->getIconImage()) {
            $data['iconImage'] = $location->getIconImage()->getId();
        }
        $data['UserApi'][$location->getUserApi()->getUsername()] = $location->getUserApi()->getId();

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
                $response['data']['devices'][] = $item->getAsArrayClient();
            } else {
                $response['data']['sensors'][] = $item->getAsArrayClient();
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
            $this->addFlash('danger', 'Publisher not added! Something went wrong');
            return $this->redirectToRoute('index');
        }
        $publisher->setLocation($location);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $em->persist($publisher);
                $notice = new Notice(
                    [
                        'location' => $location,
                        'action' => 2,
                        'text' => sprintf(
                            'Publisher "%s" was added to the "%s"',
                            $publisher->getName(),
                            $location->getName()
                        ),
                        'time' => (new \DateTime())->setTimestamp(time())
                    ]
                );
                $em->persist($notice);
                $em->flush();
                $this->addFlash('success', 'Publisher has been successfully added!');
            } catch (ErrorException) {
                $this->addFlash('danger', 'Something went wrong!');
            }
        }

        return $this->redirectToRoute('app_location', [
            '_fragment' => $locationId
        ]);
    }

    /**
     * @param Publisher $publisher
     * @param EntityManagerInterface $em
     * @param UserInterface $user
     * @param Request $request
     * @return Response
     */
    public function editPublisher
    (
        Publisher $publisher,
        EntityManagerInterface $em,
        UserInterface $user,
        Request $request
    ): Response
    {
        if (!$em->getRepository(Publisher::class)->findUserPublisher($user->getId(), $publisher->getId())) {
            $this->addFlash('danger', 'You do not have access to this publisher!');

            return $this->redirectToRoute('index');
        }
        $form = $this->createForm(PublisherDataType::class, $publisher);
        $data = [];
        $data['form'] = $form;
        $type = $publisher->getType();
        $form->handleRequest($request);
        $publisher->setType($type);
        $data['name'] = $publisher->getName();
        $data['id'] = $publisher->getId();
        $data['type'] = $publisher->getType();
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $em->persist($publisher);
                $em->flush();
                $this->addFlash('success', 'The publisher has been successfully edited!');
            } catch (ErrorException) {
                $this->addFlash('danger', 'Something went wrong!');
            }
        }

        return $this->render('app/edit_publisher.html.twig', $data);
    }

    /**
     * @param Publisher $publisher
     * @param EntityManagerInterface $em
     * @param UserInterface $user
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deletePublisher
    (
        Publisher $publisher,
        EntityManagerInterface $em,
        UserInterface $user
    )
    {
        if (!in_array($publisher, $em->getRepository(Location::class)->findUserPublishers($publisher->getLocation()->getId(), $user->getId()))) {
            $this->addFlash('danger', 'You do not have access to this publisher!');

            return $this->redirectToRoute('index');
        }
        try {
            $name = $publisher->getName();
            $location = $publisher->getLocation();
            $em->remove($publisher);
            $notice = new Notice(
                [
                    'location' => $location,
                    'action' => 3,
                    'text' => sprintf(
                        'Publisher "%s" was removed from the "%s"',
                        $publisher->getName(),
                        $location->getName()
                    ),
                    'time' => (new \DateTime())->setTimestamp(time())
                ]
            );
            $em->persist($notice);
            $em->flush();
            $this->addFlash('success', sprintf('Publisher "%s" was deleted', $name));
        } catch (ErrorException) {
            $this->addFlash('danger', 'Something went wrong!');
        }

        return $this->redirectToRoute('index');
    }

    public function editPlate
    (
        UserApi $plate,
        UserInterface $user
    ): Response
    {
        $form = $this->createForm(UserApiEditType::class, $plate);
        return $this->render('app/edit_plate.html.twig', [
            'form' => $form,
            'name' => $plate->getUsername(),
            'id' => $plate->getId()
        ]);
    }

    public function changePlate
    (
        UserApi $plate,
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $em,
        UserInterface $user
    )
    {
        $form = $this->createForm(UserApiEditType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $plate->setUsername($form->get('username')->getData());
            try {
                $em->persist($plate);
                $em->flush();
                $this->addFlash('success', 'IoT has been successfully edited!');
            } catch (ErrorException) {
                $this->addFlash('danger', 'Something went wrong!');
            }
//            $hashedPassword = $passwordHasher->hashPassword(
//                $plate,
//                $form->get('old_password')->getData()
//            );
//            VarDumper::dump($form->get('old_password')->getData());
//            VarDumper::dump($hashedPassword);
//            VarDumper::dump($plate->getPassword());
//            if ($plate->getPassword() == $hashedPassword) {
//                VarDumper::dump('+');
//            } else {
//                VarDumper::dump('-');
//            }
        }

        return $this->redirectToRoute('user_account');
    }

    public function deletePlate
    (
        UserApi $plate,
        EntityManagerInterface $em,
        UserInterface $user
    )
    {
        if ($plate->getUser() !== $user) {
            $this->addFlash('danger', 'You do not have access to this IoT!');
            return $this->redirectToRoute('index');
        }
        try {
            $em->remove($plate);
            $em->flush();
        } catch (ErrorException) {
            $this->addFlash('danger', 'Something went wrong!');
        }
        return $this->redirectToRoute('user_account');
    }

    /**
     * @return Response
     */
    public function arduinoImitation(): Response
    {

        return $this->render('trash.html.twig');
    }
}
