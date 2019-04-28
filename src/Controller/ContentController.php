<?php

namespace App\Controller;

use App\Entity\Content;
use App\Form\ContentType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ContentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/content")
 */
class ContentController extends AbstractController
{

    CONST square = ['a', 'c', 'e', 'i', 'm', 'n', 'o', 'r', 's', 'u', 'v', 'w', 'x'];
    CONST longBottom = ['g', 'j', 'p', 'q', 'y', 'z'];
    CONST longTop = ['b', 'd', 'h', 'k', 'l', 't'];
    CONST long = ['f'];
    CONST accent = ['à', 'è', 'â', 'é', 'è', 'ê', 'ë' ,'ï', 'î', 'ô', 'ù', 'û'];
    CONST cedilla = ['ç'];

    protected $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/", name="content_index", methods={"GET"})
     */
    public function index(ContentRepository $contentRepository): Response
    {
        return $this->render('content/index.html.twig', [
            'contents' => $contentRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="content_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $content = new Content();
        $form = $this->createForm(ContentType::class, $content);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($content);
            $entityManager->flush();


            return $this->redirectToRoute('translated_content', [
                'id' => $content->getId()
            ]);
        }

        return $this->render('content/new.html.twig', [
            'content' => $content,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="content_show", methods={"GET"})
     */
    public function show(Content $content): Response
    {
        return $this->render('content/show.html.twig', [
            'content' => $content,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="content_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Content $content): Response
    {
        $form = $this->createForm(ContentType::class, $content);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('content_index', [
                'id' => $content->getId(),
            ]);
        }

        return $this->render('content/edit.html.twig', [
            'content' => $content,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="content_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Content $content): Response
    {
        if ($this->isCsrfTokenValid('delete'.$content->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($content);
            $entityManager->flush();
        }

        return $this->redirectToRoute('content_index');
    }


/**
 * @Route("/translated/{id}", name="translated_content", methods={"GET","POST"})
 */
    public function defineCaraterType($id)
    {
        $content = $this->em->getRepository(Content::class)->findOneById($id);
        setlocale(LC_ALL,'fr_FR.UTF-8');
//        $text = str_split($content->getText());
        $text = preg_split('//u', strtolower($content->getText()), -1, PREG_SPLIT_NO_EMPTY);
        $arrayLetters = [];
        var_dump($text);
        foreach ($text as $letter)
        {
            if(in_array($letter, self::square))
            {
                $arrayLetters [] = 'square';

            } elseif (in_array($letter, self::longBottom))
            {
                $arrayLetters [] = 'long-bottom';

            } elseif (in_array($letter, self::longTop))
            {
                $arrayLetters [] = 'long-top';

            } elseif (in_array($letter, self::long))
            {
                $arrayLetters [] = 'long';

            } elseif (in_array($letter, self::accent))
            {
                $arrayLetters [] = 'accent';

            }elseif (in_array($letter, self::cedilla))
            {
                $arrayLetters [] = 'cedilla';
            }
            elseif ($letter === " ")
            {
                $arrayLetters [] = 'space';
            }
            else
            {
                $arrayLetters [] = 'punctuation';
            }
        }
        return $this->render('result.html.twig', [
            'arrayLetters' => $arrayLetters,
            'text' => $content->getText()
        ]);
    }

    public function getHtmlStructure($arrayLetters)
    {
        foreach ($arrayLetters as $caracter)
        {
            $caracter .= $caracter;
        }

        return $caracter;
    }
}
