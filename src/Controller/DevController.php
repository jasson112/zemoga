<?php


namespace App\Controller;

use App\Service\Utils;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class DevController extends AbstractController
{
    protected $em;
    protected $mailer;
    protected $util;
    protected $session;

    public function __construct(Utils $util, EntityManagerInterface $em, SessionInterface $session){
        $this->em = $em;
        $this->util = $util;
        $this->session = $session;
    }


}