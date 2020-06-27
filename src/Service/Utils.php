<?php


namespace App\Service;


use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class Utils
{
    protected $em;
    protected $request_stack;
    protected $locale;
    protected $session;

    public function __construct(EntityManagerInterface $em, RequestStack $request_stack, Container $container, SessionInterface $session)
    {
        $this->em = $em;
        $this->request_stack = $request_stack;
        $this->container = $container;
        $this->session = $session;
    }

    public function test(){
        //$this->renderer->searchAndRenderBlock($view, "javascript", []);
        $texts = $this->em->createQueryBuilder()
            ->select('t', 'c', 'l')
            ->from("App:Text", 't')
            ->leftJoin("t.contents", "c")
            ->leftJoin("c.lang", "l")
            ->andWhere("l.name = 'en'")
            ->andWhere("t.key = 'home_title'")
            ->getQuery()
            ->getResult();
        dump($texts[0]);
    }

    public function md5($str){
        return md5($str);
    }

    public function leading($number){
        return str_pad($number, 4, '0', STR_PAD_LEFT);
    }

    public function getRegions(){
        $locale = $this->request_stack->getCurrentRequest()->getLocale();
        $qb = $this->em->createQueryBuilder()
            ->select('t', 'c', "d", "dp", "dpc", "dpcl", "dpd", "dpdc", "dpdcl", "dpe", "dpec", "dpecl")
            ->from("App:Region", 't')
            ->leftJoin("t.destinations", "d")
            ->leftJoin("t.contents", "c")
            ->leftJoin("c.lang", "l")
            ->leftJoin("d.products", "dp")
            ->leftJoin("dp.contents", "dpc")
            ->leftJoin("dpc.lang", "dpcl")
            ->leftJoin("dp.destinations", "dpd")
            ->leftJoin("dp.experiences", "dpe")
            ->leftJoin("dpe.contents", "dpec")
            ->leftJoin("dpec.lang", "dpecl")
            ->leftJoin("dpd.contents", "dpdc")
            ->leftJoin("dpdc.lang", "dpdcl")
            ->where('t.key is not null')
            ->andWhere("dpcl.name = '" . $locale . "'")
            ->andWhere("l.name = '" . $locale . "'")
            ->andWhere("dp.inMenu = 1")
            ->andWhere("dp.enabled = 1")
            ->getQuery()
            ->useQueryCache(true)
            ->getResult();
        return $qb;
    }

    public function getProductByDestination($id){
        $locale = $this->request_stack->getCurrentRequest()->getLocale();
        $qb = $this->em->createQueryBuilder()
            ->select('d', "p")
            ->from("App:Destination", 'd')
            ->leftJoin("d.products", "p")
            ->andWhere("d.id = " . $id)
            ->andWhere("p.inMenu = 1")
            ->getQuery()
            ->useQueryCache(true)
            ->useResultCache(true)
            ->getResult();
        return $qb;
    }

    public function getTextBig($key){
        $locale = $this->request_stack->getCurrentRequest()->getLocale();
        $qb = $this->em->createQueryBuilder()
            ->select('t', 'c.text as text')
            ->from("App:TextBig", 't')
            ->leftJoin("t.contents", "c")
            ->leftJoin("c.lang", "l")
            ->where('t.key is not null')
            ->andWhere("t.key = '" . $key . "'")
            ->andWhere("l.name = '" . $locale . "'")
            ->getQuery()
            ->useQueryCache(true)
            ->getOneOrNullResult();
        return $qb["text"];
    }

    public function getImg($key){
        $path =  $this->container->getParameter("app.path.galery_image");
        $locale = $this->request_stack->getCurrentRequest()->getLocale();
        $qb = $this->em->createQueryBuilder()
            //->select('i', 'CONCAT(\'' . $path . "/" . '\', i.image) as image')
            ->select('i', 'i.image as image', 'i.imageMobil as imageMobil')
            ->from("App:Image", 'i')
            ->leftJoin("i.contents", "c")
            ->leftJoin("c.lang", "l")
            ->where('i.key is not null')
            //->andWhere("l.name = '" . $locale . "'")
            ->andWhere("i.key = '" . $key . "'");

        $result = $qb->getQuery()
            ->useQueryCache(true)
            ->useResultCache(true)
            ->getOneOrNullResult();
        if ($result){
            $result["alt"] = "";
            $result["title"] = "";
            $result["description"] = "";
            foreach ($result[0]->getContents() as $key => $value){
                if(strtolower($value->getLang()->getName()) == $locale){
                    $result["alt"] = $value->getAlt();
                    $result["title"] = $value->getTitle();
                    $result["description"] = $value->getDescription();
                }
            }
        }
        return $result;
    }

    public function getImgContent($id){
        $content = [
            "alt" => "",
            "title" => "",
            "description" => ""
        ];
        $locale = $this->request_stack->getCurrentRequest()->getLocale();
        $qb = $this->em->createQueryBuilder()
            ->select('i', 'c')
            ->from("App:Image", 'i')
            ->leftJoin("i.contents", "c")
            ->leftJoin("c.lang", "l")
            ->where('i.key is not null')
            //->andWhere("l.name = '" . $locale . "'")
            ->andWhere("i.id = " . $id );

        $result = $qb->getQuery()
            ->useQueryCache(true)
            ->useResultCache(true)
            ->getOneOrNullResult();

        foreach ($result->getContents() as $key => $value){
            if(strtolower($value->getLang()->getName()) == $locale){
                $content["alt"] = $value->getAlt();
                $content["title"] = $value->getTitle();
                $content["description"] = $value->getDescription();
            }
        }
        return $content;
    }

    public function getAllImg(){
        $path =  $this->request_stack->getCurrentRequest()->getSchemeAndHttpHost() . $this->container->getParameter("app.path.galery_image");
        $locale = $this->request_stack->getCurrentRequest()->getLocale();
        $qb = $this->em->createQueryBuilder()
            ->select('i', 'CONCAT(\'' . $path . "/" . '\', i.image) as image', 'i.key as key')
            ->from("App:Image", 'i')
            ->leftJoin("i.contents", "c")
            ->leftJoin("c.lang", "l");

        $result = $qb->getQuery()
            ->useQueryCache(true)
            ->useResultCache(true)
            ->getResult();
        return $result;
    }

    public function getGallery($key){
        $qb = $this->em->createQueryBuilder()
            ->select('g', 'i')
            ->from("App:Gallery", 'g')
            ->leftJoin("g.images", "i")
            ->where('g.key is not null')
            ->andWhere("g.key = '" . $key . "'")
            ->orderBy('i.order', 'ASC')
            ->getQuery()
            ->useQueryCache(true)
            ->getOneOrNullResult();
        return $qb;
    }

    public function getGalleryContents($key){
        $locale = $this->request_stack->getCurrentRequest()->getLocale();
        $qb = $this->em->createQueryBuilder()
            ->select('g', 'i', 'c')
            ->from("App:Gallery", 'g')
            ->leftJoin("g.images", "i")
            ->leftJoin("i.contents", "c")
            ->leftJoin("c.lang", "l")
            ->where('g.key is not null')
            ->andWhere("g.key = '" . $key . "'")
            ->andWhere("l.name = '". $locale ."'" )
            ->orderBy('i.order', 'ASC')
            ->getQuery()
            ->useQueryCache(true)
            ->getOneOrNullResult();
        return $qb;
    }

    public function getConfig($key, $rich = null){
        $qb = $this->em->createQueryBuilder()
            ->select('c')
            ->from("App:Config", 'c')
            ->where('c.key is not null')
            ->andWhere("c.key = '" . $key . "'")
            ->getQuery()
            ->useQueryCache(false)
            ->useResultCache(false)
            ->getOneOrNullResult();
        if(!$qb){
            return "";
        }

        if($rich){
            return $qb->getRich();
        }
        return $qb->getValue();
    }

    public function getEncodeTpaga(){
        return base64_encode($this->getConfig("tpaga_public") . ":" . $this->getConfig("tpaga_password"));
    }

    public function getSorted($str){
        foreach ($str as $key => $val) {
            $price = explode(",",$val);
            if($price[2] == 0){
                \array_splice($str, $key, $key);
            }else{
                $str[$key] = $price;
            }
        }
        return $str;
    }

    public function getPlanning($term){
        $locale = $this->request_stack->getCurrentRequest()->getLocale();
        $qb = $this->em->createQueryBuilder();
        $query = $this->em->createQueryBuilder()
            ->select("t", "c")
            ->from("App:Text", 't')
            ->leftJoin("t.contents", "c")
            ->leftJoin("c.lang", "l")
            ->andWhere("l.name = '" . $locale . "'")
            ->andWhere(
                $qb->expr()->like("t.key", ":term")
            )->setParameter('term', $term . "%");
        $query = $query->getQuery()
            ->useQueryCache(true)
            ->useResultCache(true)
            ->getResult();
        $textArr = [];
        if($term == 'vacation_planning_step_3_kind_'){
            foreach ($query as $key => $value) {
                // $arr[3] will be updated with each value from $arr...
                $textArr[$value->getContents()->last()->getText()] = $value->getContents()->last()->getText();
            }
        }else if($term == 'vacation_planning_preferences_'){
            foreach ($query as $key => $value) {
                // $arr[3] will be updated with each value from $arr...
                $textArr[$key] = $value->getContents()->last()->getText();
            }
        }else{
            foreach ($query as $key => $value) {
                // $arr[3] will be updated with each value from $arr...
                array_push($textArr, [
                    $value->getContents()->last()->getText() => $value->getContents()->last()->getText()
                ]);
            }
        }


        return $textArr;
    }

    public function decode($str){
        return json_decode($str);
    }

    public function getProducts(){
        $path =  $this->request_stack->getCurrentRequest()->getSchemeAndHttpHost() . $this->container->getParameter("app.path.galery_image");
        $locale = $this->request_stack->getCurrentRequest()->getLocale();
        $products = $this->em->createQueryBuilder()
            ->select('p', "c", "l", "g", "t", "tc", "tcl", "pc", "pch", "pchc", "pchcl", "i")
            ->from("App:Product", 'p')
            ->leftJoin("p.contents", "c")
            ->leftJoin("c.lang", "l")
            ->leftJoin("p.gallery", "g")
            ->leftJoin("p.tripType", "t")
            ->leftJoin("t.contents", "tc")
            ->leftJoin("tc.lang", "tcl")
            ->leftJoin("p.productCategory", "pc")
            ->leftJoin("p.children", "pch")
            ->leftJoin("pch.contents", "pchc")
            ->leftJoin("pchc.lang", "pchcl")
            ->leftJoin("p.image", "i")
            ->andWhere("l.name = '" . $locale . "'")->orderBy("c.name", "asc")->getQuery()->getArrayResult();
        return $products;
    }

    public function getProduct($id = null, $trip = true, $close = false, $allLocale = false){
        $locale = $this->request_stack->getCurrentRequest()->getLocale();
        if($close){
            $this->em->clear();
        }

        $product = $this->em->createQueryBuilder()
            ->select('p', "c", "l", "g", "t", "tc", "tcl", "pc", "pch", "pchc", "pchcl")
            ->from("App:Product", 'p')
            ->leftJoin("p.contents", "c")
            ->leftJoin("c.lang", "l")
            ->leftJoin("p.gallery", "g")
            ->leftJoin("p.tripType", "t")
            ->leftJoin("t.contents", "tc")
            ->leftJoin("tc.lang", "tcl")
            ->leftJoin("p.productCategory", "pc")
            ->leftJoin("p.children", "pch")
            ->leftJoin("pch.contents", "pchc")
            ->leftJoin("pchc.lang", "pchcl");

        if(!$allLocale){
            $product->andWhere("l.name = '" . $locale . "'");
        }

        if($trip){
            $product->andWhere("tcl.name = '" . $locale . "'");
        }

        if($id){
            $product->andWhere("p.id = " . $id );
        }

        return $product->getQuery()
            ->useQueryCache(false)
            ->getOneOrNullResult();
    }

    public function getProductAddOn($id = null){
        $locale = $this->request_stack->getCurrentRequest()->getLocale();
        try{
            $content = $this->em->createQueryBuilder()
                ->select('p', 'a', 'ac', 'acl')
                ->from('App:Product', 'p')
                ->leftJoin('p.addOns', 'a')
                ->leftJoin('a.contents', 'ac')
                ->leftJoin('ac.lang', 'acl')
                ->where('p.id = '. $id)
                ->andWhere("acl.name = '". $locale ."'")
                ->getQuery()
                ->useQueryCache(true)
                ->useResultCache(true)
                ->getOneOrNullResult();
        }catch (NonUniqueResultException $e){
            return null;
        }
        return $content;
    }

    public function getGroup($id){
        try {
            $group = $this->em->createQueryBuilder()
                ->select('g')
                ->from('App:Group', 'g')
                ->where('g.id = '. $id)
                ->getQuery()
                ->useQueryCache(true)
                ->getOneOrNullResult();
        }catch (\Exception $e){
            return null;
        }

        return $group;
    }

    public function getCurrencies(){
        $currency = $this->session->get("currency", null);
        $locale = $this->request_stack->getCurrentRequest()->getLocale();
        $qb = $this->em->createQueryBuilder()
            ->select("co.title as title", "c.exchange as exchange", "c.code as code", "c.id as id")
            ->from("App:Currency", 'c')
            ->leftJoin("c.contents", "co")
            ->leftJoin("co.lang", "l")
            ->andWhere("l.name = '" . $locale . "'");
        if($currency){
            $qb->andWhere("c.id <> " . $currency["id"]);
        }
        return $qb->getQuery()
            ->useQueryCache(true)
            ->useResultCache(true)
            ->getResult();
    }

    public function getCurrency($id){
        $qb = $this->em->createQueryBuilder()
            ->select("c.exchange as exchange")
            ->from("App:Currency", 'c')
            ->andWhere("c.id = " . $id);
        return $qb->getQuery()
            ->useQueryCache(true)
            ->getOneOrNullResult();
    }

    public function getLanguages(){
        $locale = $this->request_stack->getCurrentRequest()->getLocale();
        $qb = $this->em->createQueryBuilder()
            ->select("l")
            ->from("App:Lang", 'l')
            ->andWhere("l.name <> '" . $locale . "'");
        return $qb->getQuery()
            ->useQueryCache(true)
            ->useResultCache(true)
            ->getResult();
    }

    public function currency($id = null, $code = null, $sesion = true){
        $currency = $this->session->get("currency", null);
        if(!$currency || !$sesion){
            $locale = $this->request_stack->getCurrentRequest()->getLocale();
            $currency = $this->em->createQueryBuilder()
                ->select("c.title as title", "s.exchange as exchange", "s.code as code", "s.id as id")
                ->from("App:Currency", 's')
                ->leftJoin("s.contents", "c")
                ->leftJoin("c.lang", "l")
                ->andWhere("l.name = '" . $locale . "'");
            if($id){
                $currency->andWhere("s.id = " . $id );
            }else if($code){
                $currency->andWhere("s.code = '" . $code . "'" );
            }
            $currency = $currency->getQuery()
                ->useQueryCache(true)
                ->useResultCache(true)
                ->getOneOrNullResult();
            if($sesion){
                $this->session->set("currency", $currency);
            }else{
                return $currency;
            }
        }
        return "";
    }

    public function getReviews(int $id_product){
        $locale = $this->request_stack->getCurrentRequest()->getLocale();
        $qb = $this->em->createQueryBuilder()
            ->select("r", "c")
            ->from("App:Review", 'r')
            ->leftJoin("r.product", 'p')
            ->leftJoin("r.contents", "c")
            ->leftJoin("c.lang", 'l')
            ->where('p.id = '. $id_product)
            ->andWhere("l.name = '" . $locale . "'")
            ->getQuery()
            ->useQueryCache(true)
            ->getResult();
        return $qb;
    }

    public function getRelatedBlogs(int $id_product){
        $locale = $this->request_stack->getCurrentRequest()->getLocale();
        $qb = $this->em->createQueryBuilder()
            ->select("b", "c")
            ->from("App:Blog", 'b')
            ->leftJoin("b.products", 'p')
            ->leftJoin("b.contents", "c")
            ->leftJoin("c.lang", 'l')
            ->where('p.id = '. $id_product)
            ->andWhere("l.name = '" . $locale . "'")
            ->getQuery()
            ->useQueryCache(true)
            ->getResult();
        return $qb;
    }

    public function getChilds($id){
        $locale = $this->request_stack->getCurrentRequest()->getLocale();
        $product = $this->em->createQueryBuilder()
            ->select("p", "c")
            ->from("App:Product", 'p')
            ->leftJoin("p.contents", "c")
            ->leftJoin("c.lang", "l")
            ->leftJoin("p.gallery", "g")
            ->leftJoin("p.tripType", "t")
            ->leftJoin("t.contents", "tc")
            ->leftJoin("tc.lang", "tcl")
            ->leftJoin("p.productCategory", "pc")
            ->leftJoin("p.parent", "pr")
            ->andWhere("l.name = '" . $locale . "'")
            ->andWhere("tcl.name = '" . $locale . "'")
            ->andWhere("p.enabled = 1")
            ->andWhere("pr.id = " . $id)
            ->orderBy("p.relatedOrder", "desc")
            ->getQuery()
            ->useQueryCache(true)
            ->useResultCache(true)
            ->getResult();
        return $product;
    }

    public function getDestinations(){
        $locale = $this->request_stack->getCurrentRequest()->getLocale();
        $destinations = $this->em->createQueryBuilder()
            ->select('d', "c")
            ->from("App:Destination", 'd')
            ->leftJoin("d.contents", "c")
            ->leftJoin("c.lang", "l")
            ->andWhere("l.name = '" . $locale . "'")
            ->getQuery()
            ->useQueryCache(true)
            ->getResult();
        return $destinations;
    }

    public function getExperiences(){
        $locale = $this->request_stack->getCurrentRequest()->getLocale();
        $experiences = $this->em->createQueryBuilder()
            ->select('e', "c")
            ->from("App:Experience", 'e')
            ->leftJoin("e.contents", "c")
            ->leftJoin("c.lang", "l")
            ->andWhere("l.name = '" . $locale . "'")
            ->andWhere("e.enabled = 1")
            ->andWhere("e.transport = 0")
            ->orderBy("e.order", "asc")
            ->getQuery()
            ->useQueryCache(true)
            ->getResult();
        return $experiences;
    }

    public function getDayType(){
        $locale = $this->request_stack->getCurrentRequest()->getLocale();
        $dayType = $this->em->createQueryBuilder()
            ->select('d', "c")
            ->from("App:DayType", 'd')
            ->leftJoin("d.contents", "c")
            ->leftJoin("c.lang", "l")
            ->andWhere("l.name = '" . $locale . "'")
            ->getQuery()
            ->useQueryCache(true)
            ->getResult();
        return $dayType;
    }

    public function getDayDuration($duration){
        $pos = strpos($duration,$this->getText('day'));
        return intval(substr($duration,0,$pos));
    }

    public function getPriceType(){
        $locale = $this->request_stack->getCurrentRequest()->getLocale();
        $priceType = $this->em->createQueryBuilder()
            ->select('p', "c")
            ->from("App:PriceType", 'p')
            ->leftJoin("p.contents", "c")
            ->leftJoin("c.lang", "l")
            ->andWhere("l.name = '" . $locale . "'")
            ->getQuery()
            ->useQueryCache(true)
            ->getResult();
        return $priceType;
    }

    public function getXpImg($xp){
        $destinations = $this->em->createQueryBuilder()
            ->select('e', "ib")
            ->from("App:Experience", 'e')
            ->leftJoin("e.myimageback", "ib")
            ->andWhere("e.name = '" . $xp . "'")
            ->getQuery()
            ->useQueryCache(true)
            ->useResultCache(true)
            ->getOneOrNullResult();
        return $destinations;
    }

    public function getTripTypeContent($id){
        $locale = $this->request_stack->getCurrentRequest()->getLocale();
        $content = $this->em->createQueryBuilder()
            ->select('t', "tc", "tcl")
            ->from("App:TripType", 't')
            ->leftJoin("t.contents", "tc")
            ->leftJoin("tc.lang" , "tcl")
            ->andWhere("t.id = " . $id)
            ->andWhere("tcl.name = '" . $locale . "'")
            ->getQuery()
            ->useQueryCache(true)
            ->useResultCache(true)
            ->getOneOrNullResult();
        return $content;
    }

    public function getSpecialistContent($id){
        $locale = $this->request_stack->getCurrentRequest()->getLocale();
        try{
            $content = $this->em->createQueryBuilder()
                ->select('s', "sc", "scl", "i")
                ->from("App:Specialist", 's')
                ->leftJoin("s.contents", "sc")
                ->leftJoin("sc.lang" , "scl")
                ->leftJoin("s.image" , "i")
                ->andWhere("s.id = " . $id)
                ->andWhere("scl.name = '" . $locale . "'")
                ->getQuery()
                ->useQueryCache(true)
                ->useResultCache(true)
                ->getOneOrNullResult();
        }catch (NonUniqueResultException $e){
            return null;
        }
        return $content;
    }

    public function getItemContent($id){
        $locale = $this->request_stack->getCurrentRequest()->getLocale();
        $content = $this->em->createQueryBuilder()
            ->select( "i", "c", "l")
            ->from("App:item", 'i')
            ->leftJoin("i.contents", "c")
            ->leftJoin("c.lang", "l")
            ->andWhere("i.id = " . $id)
            ->andWhere("l.name = '" . $locale . "'")
            ->getQuery()
            ->useQueryCache(true)
            ->useResultCache(true)
            ->getOneOrNullResult();
        return $content;
    }

    public function getDiscount($id){
        $discountBy = $this->em->getRepository("App:Discount")->find($id);
        return $discountBy;
    }

    public function getTravelStyle(){
        $locale = $this->request_stack->getCurrentRequest()->getLocale();
        return $this->em->createQueryBuilder()
            ->select( "t", "i", "c")
            ->from("App:TravelStyle", 't')
            ->leftJoin("t.icon", "i")
            ->leftJoin("t.contents", 'c')
            ->leftJoin("c.lang", "l")
            ->where("l.name ='" . $locale . "'")
            ->getQuery()
            ->getResult();
    }

    public function getTravelActivities(){
        $locale = $this->request_stack->getCurrentRequest()->getLocale();
        return $this->em->createQueryBuilder()
            ->select( "t", "i", "c")
            ->from("App:TravelActivity", 't')
            ->leftJoin("t.image", "i")
            ->leftJoin("t.contents", 'c')
            ->leftJoin("c.lang", "l")
            ->where("l.name ='" . $locale . "'")
            ->getQuery()
            ->getResult();
    }

    public function slugifySeo($text){
        $unwanted_array = array(    'Š'=>'S', 'š'=>'s', 'Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
            'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U',
            'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss', 'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c',
            'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o',
            'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y', "&" => "y" );

        $text = strtr( $text, $unwanted_array );

        return $text;
    }

    public function slugify($text, $is_destination = false)
    {
        $unwanted_array = array(    'Š'=>'S', 'š'=>'s', 'Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
            'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U',
            'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss', 'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c',
            'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o',
            'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y', "&" => "y" );

        $text = strtr( $text, $unwanted_array );

        // replace non letter or digits by -
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);



        // transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);

        // trim
        $text = trim($text, '-');

        // remove duplicate -
        $text = preg_replace('~-+~', '-', $text);

        // lowercase
        $text = strtolower($text);

        if (empty($text))
        {
            return 'n-a';
        }
        if($is_destination && $text != "all-locations" && !strpos($text, "-tours")){
            return $text . "-tours";
        }
        return $text;
    }

    function getUniqid(){
        return uniqid("", true);
    }

    function getActive($id, $ajax = 1, $inFoot = false){
        try{
            $ac = new \ActiveCampaign($this->getConfig("active_api_url"), $this->getConfig("active_api_key"));

            if($ac->credentials_test()){
                $form_embed_params = array(
                    "id" => $id, // subscription form ID
                    "action" => "form-process", // custom form action attribute
                    "ajax" => $ajax, // whether or not to use Ajax to submit the form
                    "css" => 0, // whether or not to retain the CSS
                    'api_output'   => 'serialize'
                );
                $api_params = array();
                foreach ($form_embed_params as $var => $val) {
                    $api_params[] = $var . "=" . $val;
                }

                $form_html = $ac->api("form/html?" . implode("&", $api_params));
                // Remove style on forms
                $form_html = preg_replace("/<style[^>]*>(.*)<\/style>/s", "", $form_html);
                // Remove Embedded forms JS
                $form_html = preg_replace('/<script[^>]*>.*?<\/script>/s', '', $form_html);
                if($ajax == 1){

                    // if using Ajax, remove the <form> action attribute completely
                    $form_html = preg_replace("/action=['\"][^'\"]+['\"]/", "", $form_html);

                    // replace the Submit button to be a button type (for ajax).
                    // forms come out of AC now with a "submit" button (it used to be "button").
                    $form_html = preg_replace("/class=['\"]+_submit['\"]+ type=['\"]+submit['\"]+/", "class='_submit' type='button'", $form_html);

                    // Replace the external image (captcha) script with the local one, so the session var is accessible.
                    $form_html = preg_replace("/\/\/.*\/ac_global\/scripts\/randomimage\.php/i", "randomimage.php", $form_html);

                    $form_html = preg_replace("/%TC%\*/", $this->getText('check_footer') , $form_html);

                    $action_val = urldecode("/public/en/form-process");
                    // change structure of html
                    if($id == $this->getConfig('form_footer') && $inFoot){
                        //first change
//                        $text = 'By checking this box you confirm that you agree to our <a href="https://impulsetravel.co/tour-operator/en/privacy-policy" target="_blank">Privacy Policy</a> & <a href="https://impulsetravel.co/tour-operator/en/terms-and-conditions" target="_blank">Terms and Conditions</a></label>';
//                        $form_html = str_replace('%TC%*</label>', $text, $form_html);
//                        $form_html = substr_replace($form_html, $text, $pos, 0);

                        //second change
//                        $text = 'By checking this box you confirm that you agree to our <a href="https://impulsetravel.co/tour-operator/en/privacy-policy" target="_blank">Privacy Policy</a> & <a href="https://impulsetravel.co/tour-operator/en/terms-and-conditions" target="_blank">Terms and Conditions</a></label>';
//                        $pos = strpos($form_html, '<label for=field_39%TC%>');
//                        dump($pos);
//                        $form_html = substr_replace($form_html, $text, $pos, 4);

                    }

                    // add jQuery stuff
                    $extra = "<script type='text/javascript'>
                        window.globals.activeCampaing['{$id}'] = function(){
                            \$('#_form_{$id}_ button').click(function() {
                                $('#_form_{$id}_').LoadingOverlay('show', {
                                    image   : '',
                                    custom  : CustomLoader
                                });
                                animateLoader();
                                // rename the radio options for Subscribe/Unsubscribe, since they conflict with the hidden field.
                                \$('input[type=radio][name=act]').attr('name','act_radio');
                        
                                var form_data = {};
                                \$('#_form_{$id}_').each(function() {
                                    form_data = \$(this).serialize();
                                });
                                
                                var trackcmp_email = $('#_form_{$id}_').find('input[name=email]').val();
                                var trackcmp = document.createElement(\"script\");
                                trackcmp.async = true;
                                trackcmp.type = 'text/javascript';
                                trackcmp.onload = function() {
                                    var geturl;
                                    geturl = \$.ajax({
                                        url: '{$action_val}',
                                        type: 'POST',
                                        dataType: 'json',
                                        data: form_data,
                                        error: function(jqXHR, textStatus, errorThrown) {
                                            $('#_form_{$id}_').LoadingOverlay('hide', true);
                                            stopLoader();
                                            console.log(errorThrown);
                                        },
                                        success: function(data) {
                                            \$('#form_{$id}_result_message').html(data.message);
                                            var result_class = (data.success) ? 'form_result_success' : 'form_result_error';
                                            \$('#form_{$id}_result_message').removeClass('form_result_success form_result_error').addClass(result_class);
                                            if(data.success){
                                                window.location = $" . "raiz + '/thanks';       
                                            }else{
                                                $('#_form_{$id}_').LoadingOverlay('hide', true);
                                                stopLoader();
                                            }
                                        }
                                    });
                                };
                                trackcmp.src = '//trackcmp.net/visit?actid=89339274&e='+encodeURIComponent(trackcmp_email)+'&r='+encodeURIComponent(document.referrer)+'&u='+encodeURIComponent(window.location.href);
                                var trackcmp_s = document.getElementsByTagName(\"script\");
                                if (trackcmp_s.length) {
                                    trackcmp_s[0].parentNode.appendChild(trackcmp);
                                } else {
                                    var trackcmp_h = document.getElementsByTagName(\"head\");
                                    trackcmp_h.length && trackcmp_h[0].appendChild(trackcmp);
                                }
                                
                        
                            });
                        
                        }
                        </script>";

                    $form_html = $form_html . $extra;
                }


                return $form_html;
            }
        }catch (\Exception $e){
            return null;
        }
        return null;
    }
}