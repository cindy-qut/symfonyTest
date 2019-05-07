<?php


namespace App\ApiController;
use App\Entity\Type;
use App\Form\TypeType;
use App\Repository\TypeRepository;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/type", host="api.exosymfony.fr")
 */
class TypeController extends AbstractFOSRestController
{
    /**
     * @Rest\Get("/", name="typelist_api")
     * @Rest\View()
     */

    public function index(TypeRepository $typeRepository): View
    {
        $types = $typeRepository->findAll();
        return View::create($types, Response::HTTP_OK);
    }

    /**
     * @Rest\Get(
     *     path="/{id}",
     *     name="typeshow_api",
     *     )
     * @Rest\View
     */
    public function show(Type $type):View
    {
        return View::create($type, Response::HTTP_OK);
    }

    /**
     * create a Type
     * @Rest\Post(
     *     path="/new",
     *     name="typecreate_api",
     *     )
     * @Rest\View
     */
    public function new(Request $request): View
    {
       $type = new Type();
       $type ->setName($request ->get('name'));
       $em = $this->getDoctrine()->getManager();
       $em->persist($type);
       $em->flush();
       return View::create($type, Response::HTTP_CREATED);
    }

    /**
     * Delete a Type
     * @Rest\Delete(
     *     path="/{id}",
     *     name="typedelete_api",
     *     )
     * @Rest\View()
     */
    public function delete (Type $type): View
    {
        if($type){
            $em = $this->getDoctrine()->getManager();
            $em->remove($type);
            $em->flush();
        }
        return View::create([], Response::HTTP_NO_CONTENT);
    }


    /**
     * Edit a Type
     * @Rest\Put(
     *     path="/{id}",
     *     name="typeedit_api",
     *     )
     * @Rest\View()
     */
    public function edit(Request $request, Type $type): View
    {
        if($type){
            $type ->setName($request ->get('name'));
            $em = $this->getDoctrine()->getManager();
            $em->persist($type);
            $em->flush();
        }
        return View::create($type, Response::HTTP_OK);
    }

    /**
     * Edit a Type
     * @Rest\Patch(
     *     path="/{id}",
     *     name="typepatch_api",
     *     )
     * @Rest\View()
     */
    public function patch(Request $request, Type $type): View
    {
        if($type){
            $form =$this->createForm(TypeType::class, $type);
            $form->submit($request->request->all(), false);
            $em = $this->getDoctrine()->getManager();
            $em->persist($type);
            $em->flush();
        }
        return View::create($type, Response::HTTP_OK);
    }

}