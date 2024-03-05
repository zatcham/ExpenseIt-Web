<?php

namespace App\Controller\Api;

use App\Entity\Receipt;
use DateTime;
use DateTimeInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RequestsApiController extends AbstractController
{
    #[Route('/api/requests/get', name: 'api_requests_get')]
    public function getUsersRequests(): JsonResponse
    {
        $requests = $this->getUser()->getRequests();
        $requests_arr = array();

        foreach ($requests as $request) {
            $id = $request->getId();
            $requests_arr[$id]['merchant'] = $request->getMerchant();
            $requests_arr[$id]['cost'] = $request->getPrice();
            $requests_arr[$id]['timestamp'] = $request->getTimestamp();
            $requests_arr[$id]['status'] = $request->getStatus();
            $requests_arr[$id]['active'] = $request->isActive();
        }

        return $this->json([
          'requests' => $requests_arr,
        ]);
    }

    #[Route('/api/requests/new', name: 'api_requests_new')]
    public function createNewRequest(Request $request, EntityManagerInterface $em) : Response {

        $data = json_decode($request->getContent(), true);
        $user = $this->getUser();
//        $timestamp = date('c', time());
        if (!isset($data['timestamp'])) {
            $timestamp = new \DateTime('now'); // Check if app has sent timestamp; if not assume current time
        } else {
            $timestamp = DateTime::createFromFormat('Y-m-d H:i', $data['timestamp']); // Convert from 2023-03-05 14:35 to DateTime obj
        }
        $timestamp->format(DateTimeInterface::ATOM);


        if (!isset($data['merchant'], $data['cost'])) { // Timestamp isn't necessary...
            return $this->json(['message' => 'Invalid Data'], Response::HTTP_BAD_REQUEST);
        }

        $rq = new \App\Entity\Request();
        // Status assumed to be pending?
        // Allow for multiple depts for user? TODO
        $rq->setUser($user);
        $rq->setPrice($data['cost']); // What retard added cost and price ....
        $rq->setActive(true);
        $rq->setStatus('pending');
        $rq->setMerchant($data['merchant']);
        $rq->setTimestamp($timestamp);

        $em->persist($rq);
        $em->flush();

        return $this->json(['message' => 'success', 'id' => $rq->getId()], Response::HTTP_OK);

    }

    #[Route('/api/requests/receipt_upload', name: 'api_requests_upload_receipt')]
    public function uploadReceipt(Request $request, EntityManagerInterface $em) : Response {

        $data = json_decode($request->getContent(), true);
        if (!isset($data['request_id'], $data['image'])) {
            return $this->json(['message' => 'Invalid Data'], Response::HTTP_BAD_REQUEST);
        }

        $req = $em->getRepository(\App\Entity\Request::class)->find($data['request_id']);

        $receipt = new Receipt();
        $receipt->setRelation($req); // Why did i call it relation..
        $receipt->setImage($data['image']);

        $em->persist($receipt);
        $em->flush();

        return $this->json(['message' => 'success', 'id' => $receipt->getId()], Response::HTTP_OK);
    }
}