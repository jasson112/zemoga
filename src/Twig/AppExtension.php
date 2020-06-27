<?php
namespace App\Twig;

use Symfony\Bridge\Twig\Node\SearchAndRenderBlockNode;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{

    public function getFilters()
    {
        return array(
            new TwigFilter('bigtags', array($this, 'bigtagsFilter')),
            new TwigFilter('upperWords', array($this, 'upperWordsFilter')),
        );
    }
    //youtube: %youtube;https://www.youtube.com/embed/cfudXO_vzWk%
    //pdf: %pdf;url_pdf%
    public function bigtagsFilter($someting)
    {
        preg_match('/%.*%/i', $someting, $result);

        foreach ($result as $clave => $valor) {
            preg_match('/%.*;/i', $valor, $pre_res);
            $find = str_replace(["%", ";"], "", $pre_res);
            if(count($find) > 0){
                $find = $find[0];
                switch ($find){
                    case "youtube":
                        $finalvalor = str_replace(["%youtube;", "%"], "", $valor);
                        $content = '<iframe width="700" height="450" src="' . $finalvalor . '"></iframe>';
                        $someting = str_replace($result, $content , $someting);
                    break;
                    case "pdf":
                        $finalvalor = str_replace(["%pdf;", "%"], "", $valor);
                        $content = '<object width="700" height="450" data="' .$finalvalor. '" type="application/pdf">
                                        <embed src="' .$finalvalor. '" type="application/pdf" />
                                    </object>';
                        $someting = str_replace($result, $content , $someting);
                    break;
                }
            }
        }
        return $someting;
    }

    //youtube: %youtube;https://www.youtube.com/embed/cfudXO_vzWk%
    public function upperWordsFilter($someting)
    {
        return ucwords($someting);
    }
}