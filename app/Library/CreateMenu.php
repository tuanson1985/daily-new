<?php
use App\Library\Helpers;

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 23/01/2015
 * Time: 9:46 PM
 */

class CreateMenu {

// Getter for the HTML menu builder
    public static function getHTML($items)
    {
        return self::buildMenu($items);
    }

   public static function buildMenu($menu, $parrent_id = 0)
    {
        $result = null;
        foreach ($menu as $item)
            if ($item->parent_id == $parrent_id) {
                $result.="<li class=".Helpers::SetActiveLink([$item->seo_linksearch]).">";
                //check để tạo cấp đâu tiên
                if($parrent_id == 0)
                {
                    //dùng tạo icon
//                     $result.= "<a href=\"/cat/".$item->id.'/'.Str::slug($item->title,'-')."\"><img src=\"$item->image\" class=\"icon\" /> <span>$item->title</span>";
                    //dùng link không icon
                    if($item->type_url==0){
                        $result.= "<a href=\"/".Html::entities($item->url)."\">".Html::entities($item->title);
                    }
                    else{
                        $result.= "<a href=\"/".Html::entities($item->seo_linksearch)."\">".Html::entities($item->title);
                    }

                    //check item co' children ko?
//                    foreach ($menu as $checkChildren) {
//                       if($checkChildren->idparrent==$item->id)
//                       {
//                           $result.="<i class=\"glyphicon glyphicon-chevron-right\"></i>";
//                           break;
//                       }
//
//                    }

                     $result.="</a>";
                }
                else
                {
                    $result.= "<a href=\"/".Html::entities($item->seo_linksearch)."\">".Html::entities($item->title)."</a>";
                }

                $result .= self::buildMenu($menu, $item->id) . "</li>";
            }
        if($result){
            if($parrent_id==0)
            {
                return "\n<ul  class=\"list-unstyled menu-category\">\n$result</ul>\n";
            }
            else{
                return "\n<ul id=\"children-of-".$parrent_id."\" class=\"list-unstyled \" aria-expanded=\"true\">\n$result</ul>\n";
            }
        }
        else{
            return null;
        }

//        return $result ? "\n<ul id=\"children-of-".$parrent_id."\" class=\"list-unstyled collapse  children-of-".$parrent_id."\">\n$result</ul>\n" : null;
    }


}
