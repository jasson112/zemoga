<?php


namespace App\Controller;


use App\Service\Utils;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class AppController  extends AbstractController
{
    protected $em;
    protected $util;
    protected $serializer;
    protected $session;
    public function __construct(EntityManagerInterface $em, Utils $util, SerializerInterface $serializer, SessionInterface $session){
        $this->em = $em;
        $this->util = $util;
        $this->serializer = $serializer;
        $this->session = $session;
    }

    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        $portfolio = $this->em->createQueryBuilder()
            ->select('p')
            ->from("App:Portfolio", 'p')
            ->getQuery()
            ->useQueryCache(true)
            ->getOneOrNullResult();
        if($portfolio){
            $twitter = new TwitterZem($portfolio->getTwitterUsername());
            $timeline = $twitter->getTimeLine();
        }
        return $this->render('app/home.html.twig', compact("portfolio", "timeline"));
    }

    /**
     * @Route("/page-not-found", name="page_not_found")
     */
    public function pageNotFoundAction(Request $request)
    {
        $response = $this->render("misc/404.html.twig");
        $response->setStatusCode(404);
        return $response;
    }

    /**
     * @Route("/server-error", name="server_error")
     */
    public function serverErrorAction(Request $request)
    {
        $response = $this->render("misc/500.html.twig");
        $response->setStatusCode(500);
        return $response;
    }

    /**
     * @Route("/shall-no-pass", name="shall_no_pass")
     */
    public function shallNoPassAction(Request $request)
    {
        return $this->render("misc/shall_no_pass.html.twig");
    }
}