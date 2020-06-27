<?php


namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Debug\Exception\FlattenException;
use Symfony\Component\HttpKernel\Log\DebugLoggerInterface;

class ExceptionController extends AbstractController
{
    /**
     * Converts an Exception to a Response.
     *
     * @param FlattenException     $exception A FlattenException instance
     * @param DebugLoggerInterface $logger    A DebugLoggerInterface instance
     * @param string               $format    The format to use for rendering (html, xml, ...)
     * @param Boolean              $embedded  Whether the rendered Response will be embedded or not
     *
     * @throws \InvalidArgumentException When the exception template does not exist
     */
    public function exceptionAction(FlattenException $exception, DebugLoggerInterface $logger = null, $format = 'html', $embedded = false)
    {
        $code = $exception->getStatusCode();
        if(strpos($exception->getMessage(), "wp-login.php") || strpos($exception->getMessage(), "wp-admin.php" || strpos($exception->getMessage(), "admin"))){
            $code = 505;
        }
        $route = "page_not_found";
        switch($code){
            case 500:
                $route = "server_error";
                break;

            case 401:
            case 403:
            case 405:
            case 406:
            case 502:
            case 505:
                $route = "shall_no_pass";
                break;

        }
        return $this->redirectToRoute($route);
    }
}