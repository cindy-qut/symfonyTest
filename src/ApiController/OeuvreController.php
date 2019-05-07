<?php


namespace App\ApiController;

use App\Repository\OeuvreRepository;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/oeuvre", host="api.exosymfony.fr")
 */
class OeuvreController extends AbstractFOSRestController
{
    /**
     * @Route("/", name="oeuvrelist_api", methods={ "GET" })
     * @Rest\View()
     */

    public function index(OeuvreRepository $oeuvreRepository): View
    {
        $oeuvres = $oeuvreRepository->findAll();
        return View::create($oeuvres, Response::HTTP_OK);
    }
}