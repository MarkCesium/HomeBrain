<?php

namespace App\Controller;

use App\Entity\Publisher;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;

class IntegratedSystemController extends AbstractController
{
    /**
     * @param EntityManagerInterface $em
     * @param UserInterface $user
     * @return Response
     */
    public function getSensors
    (
        EntityManagerInterface $em,
        UserInterface $user
    ): Response
    {
        $sensors = $em->getRepository(Publisher::class)->findUserPublishers($user->getUser()->getId(), 2);
        $sensorsArray = [];
        foreach ($sensors as $item) {
            $sensorsArray[] = $item->getAsArrayAPI();
        }

        return $this->json([
            'data' => $sensorsArray,
            'status' => 1
        ]);
    }

    /**
     * @param EntityManagerInterface $em
     * @param UserInterface $user
     * @return Response
     */
    public function getDevices
    (
        EntityManagerInterface $em,
        UserInterface $user
    ): Response
    {
        $sensors = $em->getRepository(Publisher::class)->findUserPublishers($user->getUser()->getId(), 1);
        $sensorsArray = [];
        foreach ($sensors as $item) {
            $sensorsArray[] = $item->getAsArrayAPI();
        }

        return $this->json([
            'data' => $sensorsArray,
            'status' => 1
        ]);
    }

    public function helloWorld(): Response
    {
        return $this->json(['msg' => 'Hello, World!']);
    }
}
