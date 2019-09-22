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


class ContentController extends AbstractController
{

    CONST square = ['a', 'c', 'e', 'i', 'm', 'n', 'o', 'r', 's', 'u', 'v', 'w', 'x'];
    CONST longBottom = ['g', 'j', 'p', 'q', 'y', 'z'];
    CONST longTop = ['b', 'd', 'h', 'k', 'l', 't'];
    CONST long = ['f'];
    CONST accent = ['à', 'è', 'â', 'é', 'è', 'ê', 'ë' ,'ï', 'î', 'ô', 'ù', 'û'];
    CONST cedilla = ['ç'];
    CONST upperKase = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];
    CONST punctuation = ['!', ',', '.', ';', '?', ':'];
    protected $em;

    public function __construct(
        EntityManagerInterface $em
    )
    {
        $this->em = $em;
    }

    /**
     * @Route("/", name="home", methods={"GET"})
     */
    public function home(): Response
    {
        return $this->render('home.html.twig');
    }

    /**
     * @Route("/index", name="content_index", methods={"GET"})
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

        $contentWithReturn = str_replace("\n", '_', $content->getText());

        $words = explode(" ", $contentWithReturn);

        $arraySentence = [];
        $lengthline = 0;
        $i = 1;

        foreach ($words as $word) {
            $lengthline += strlen($word) + 1;
            $wordArray = preg_split('//u', $word, -1, PREG_SPLIT_NO_EMPTY);
            $arrayWord = [];

            // defines nb caracters by printed line
            if ($lengthline <= 35)
            {
                foreach ($wordArray as $letter)
                {
                        $arrayWord = $this->replaceWordBySigns($letter, $arrayWord);
                }
                $arraySentence[$i][] = $arrayWord;
            }else{
                $lengthline = 0;
                $lengthline += strlen($word) +1;
                foreach ($wordArray as $letter)
                {
                    $arrayWord = $this->replaceWordBySigns($letter, $arrayWord);
                }
                $i++;
                $arraySentence[$i][] = $arrayWord;
            }

        }
        return $this->render('result.html.twig', [
            'arraySentence' => $arraySentence,
            'text' => $content->getText()
        ]);
    }

    /**
     * @param $letter
     * @param array $arrayWord
     * @return array
     */
    public function replaceWordBySigns($letter, array $arrayWord): array
    {
        if (in_array($letter, self::upperKase)) {
            $arrayWord [] = 'upperKase';

        } elseif (in_array($letter, self::longBottom)) {
            $arrayWord [] = 'long-bottom';

        } elseif (in_array($letter, self::square)) {
            $arrayWord [] = 'square';

        } elseif (in_array($letter, self::longTop)) {
            $arrayWord [] = 'long-top';

        } elseif (in_array($letter, self::long)) {
            $arrayWord [] = 'long';

        } elseif (in_array($letter, self::accent)) {
            $arrayWord [] = 'accent';

        } elseif (in_array($letter, self::cedilla)) {
            $arrayWord [] = 'cedilla';

        } elseif ($letter === " ") {
            $arrayWord [] = 'space';

        }elseif ($letter === "_"){
            $arrayWord [] = 'returnKase';

        } elseif (in_array($letter, self::punctuation)) {
            $arrayWord [] = 'punctuation';
        }
        return $arrayWord;
    }

}
