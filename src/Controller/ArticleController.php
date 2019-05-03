<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Image;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Tests\Compiler\I;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/article")
 */
class ArticleController extends AbstractController
{
    /**
     * @Route("/", name="article_index", methods={"GET"})
     */
    public function index(ArticleRepository $articleRepository): Response
    {
        return $this->render('article/index.html.twig', [
            'articles' => $articleRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="article_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            /** @var  Symfony\Component\HttpFoundation\File\UploadedFile $file */
            $file = $form->get('image')->get('file')->getData();
            if($file){
                $image = new Image();
                $fileName = $this->generateUniqueFileName().'.'.$file->guessExtension();
                try{
                    $file->move(
                        $this->getParameter('image_abs_path'),
                        $fileName);
                } catch (FileException $e){

                }
                $image->setName($form->get('image')->get('name')->getData());
                $image->setPath($this->getParameter('image_abs_path').'/'.$fileName);
                $image->setImagePath($this->getParameter('image_path').'/'.$fileName);
                $entityManager->persist($image);
                $article->setImage($image);
            }
            else{
                $article->setImage(null);
            }
            $entityManager->persist($article);
            $entityManager->flush();

            return $this->redirectToRoute('article_index');
        }

        return $this->render('article/new.html.twig', [
            'article' => $article,
            'form' => $form->createView(),
        ]);
    }
    /**
     * @return string
     */
    private function generateUniqueFileName(){
        return md5(uniqid());
    }

    /**
     * @Route("/{id}", name="article_show", methods={"GET"})
     */
    public function show(Article $article): Response
    {
        return $this->render('article/show.html.twig', [
            'article' => $article,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="article_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Article $article): Response
    {
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $entityManager = $this->getDoctrine()->getManager();
            /** @var  Symfony\Component\HttpFoundation\File\UploadedFile $file */
            $image = $article->getImage();
            $file = $form->get('image')->get('file')->getData();
            if($file)
            {
                $fileName = $this->generateUniqueFileName().'.'.$file->guessExtension();
                try{
                    $file->move(
                        $this->getParameter('image_abs_path'),
                        $fileName);
                } catch (FileException $e){

                }
                $this->removeFile($image->getPath());
                $image->setName($form->get('image')->get('name')->getData());
                $image->setPath($this->getParameter('image_abs_path').'/'.$fileName);
                $image->setImagePath($this->getParameter('image_path').'/'.$fileName);
                $entityManager->persist($image);
                $article->setImage($image);
            }
            if(empty($image->getId())&& !$file){
                $article->setImage(null);
            }
            $entityManager->persist($article);
            $entityManager->flush();

            return $this->redirectToRoute('article_index', [
                'id' => $article->getId(),
            ]);
        }

        return $this->render('article/edit.html.twig', [
            'article' => $article,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="article_img_delete", methods={"POST"})
     */
    public function deleteImg(Request $request, Article $article): Response
    {
        if ($this->isCsrfTokenValid('delete'.$article->getId(), $request->request->get('_token'))) {
            $image=$article->getImage();
            $this->removeFile($image->getPath());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($image);
            $article->setImage(null);
            $entityManager->persist($article);
            $entityManager->flush();
        }

        return $this->redirectToRoute('article_edit', array('id'=>$article->getId()));
    }
    /**
     * @Route("/{id}", name="article_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Article $article): Response
    {
        if ($this->isCsrfTokenValid('delete'.$article->getId(), $request->request->get('_token'))) {
            $image=$article->getImage();
            if($image)
            {
                $this->removeFile($image->getPath());
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($article);
            $entityManager->flush();
        }

        return $this->redirectToRoute('article_index');
    }
    private function removeFile($path){
        if(file_exists($path)){
            unlink($path);
        }
    }
}
