<?php

namespace App\Controller;

use App\Form\ArticlesType;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Document\Articles;
use Doctrine\ODM\MongoDB\DocumentManager as DocumentManager;

class ArticlesController  extends AbstractController
{

    const PAGELIMIT=6;

    private $dm;


    public function __construct(DocumentManager $dm)
    {
        $this->dm=$dm;
    }

    /**
     * @Route("/", name="getArticles")
     */
    public function index(Request $request,PaginatorInterface $paginator): Response
    {

        $query =$this->dm->getRepository(Articles::class)->createQueryBuilder('u')
            ->getQuery();;

        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            self::PAGELIMIT /*limit per page*/
        );

        return $this->render('articles/index.html.twig', ['pagination' => $pagination]);
    }

    /**
     * @Route("/create", name="createArticle")
     */
    public function create(Request $request,DocumentManager $dm): Response
    {
        $article=new Articles();
        $form = $this->createForm(ArticlesType::class, $article);

        $form->handleRequest($request);
        if ($request->isMethod('POST') && $form->isSubmitted() && $form->isValid()) {
            $this->dm->persist($article);
            $this->dm->flush();
            $this->addFlash(
                'notice',
                'Article was save!'
            );
        }

        return $this->render('articles/create.html.twig',[
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/save_article", name="saveArticle")
     */
    public function saveArticle(Request $request): Response
    {
        $article=new Articles();
        $form = $this->createForm(ArticlesType::class, $article);
        return $this->render('articles/create.html.twig',[
            'form' => $form->createView(),
        ]);
    }
}