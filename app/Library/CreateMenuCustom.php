<?php
namespace App\Library;
use App\Library\Helpers;
use Auth;
use Html;
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 23/01/2015
 * Time: 9:46 PM
 */

class CreateMenuCustom {

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
                $result.="<li class=\"menu-item".Helpers::SetActiveLink([$item->slug])."\">";
                //check để tạo cấp đâu tiên
                if($parrent_id == 0)
                {
                    //dùng link không icon
                    if($item->url!=""){
                        $result.= "<a ".($item->target==1?"target='_blank'":"")." rel=\"".($item->target==2?Html::entities($item->url):"")."\"  href=\"".Html::entities($item->url)."\" class=\"c-link dropdown-toggle ".($item->target==2?"load-modal":"")."\">".Html::entities($item->title);
                    }
                    else{
                        $result.= "<a ".($item->target==1?"target='_blank'":"")." rel=\"".($item->target==2?Html::entities($item->url):"")."\"  href=\"/".Html::entities($item->slug)."\" class=\"c-link dropdown-toggle".($item->target==2?"load-modal":"")."\">".Html::entities($item->title);
                    }

//                    check item co' children ko?
                    foreach ($menu as $checkChildren) {
                       if($checkChildren->parrent_id==$item->id)
                       {
                           $result.="<span class=\"c-arrow c-toggler\"></span>";
                           break;
                       }

                    }

                     $result.="</a>";
                }
                else
                {
                    if($item->url!=""){
                        $result.= "<a ".($item->target==1?"target='_blank'":"")." rel=\"".($item->target==2?Html::entities($item->url):"")."\" href=\"".Html::entities($item->url)."\" class=\"".($item->target==2?"load-modal":"")."\">".Html::entities($item->title)."</a>";
                    }
                    else{
                        $result.= "<a ".($item->target==1?"target='_blank'":"")." rel=\"".($item->target==2?Html::entities($item->url):"")."\" href=\"/".Html::entities($item->slug)."\" class=\"".($item->target==2?"load-modal":"")."\">".Html::entities($item->title)."</a>";
                    }

                }

                $result .= self::buildMenu($menu, $item->id) . "</li>";
            }
        if($result){
            if($parrent_id==0)
            {
                $search="";
                $linkuser="";

                return "\n<ul  class=\"nav navbar-nav c-theme-nav\">\n$result $search $linkuser</ul>\n";
            }
            else{
                return "\n<ul id=\"children-of-".$parrent_id."\" class=\"sub-menu\" >\n$result</ul>\n";
            }
        }
        else{
            return null;
        }

//        return $result ? "\n<ul id=\"children-of-".$parrent_id."\" class=\"list-unstyled collapse  children-of-".$parrent_id."\">\n$result</ul>\n" : null;
    }

    public static function buildMenuHome($menu, $parrent_id = 0)
    {
        $result = null;
        foreach ($menu as $item)
            if ($item->parent_id == $parrent_id) {
                $result.="<li class=\" ".Helpers::SetActiveLink([$item->slug])."\">";
                //check để tạo cấp đâu tiên
                if($parrent_id == 0)
                {
                    if($item->url!=""){
                        $result.= "<a ".($item->target==1?"target='_blank'":"")." rel=\"".($item->target==2?Html::entities($item->url):"")."\"  href=\"".Html::entities($item->url)."\"  class=\"".($item->target==2?"load-modal":"")."\"><b>".Html::entities($item->title)."</b>";
                    }
                    else{
                        $result.= "<a ".($item->target==1?"target='_blank'":"")." rel=\"".($item->target==2?Html::entities($item->url):"")."\"  href=\"/".Html::entities($item->slug)."\"   class=\"".($item->target==2?"load-modal":"")."\"><b>".Html::entities($item->title)."</b>";
                    }

//                    check item co' children ko?
                    foreach ($menu as $checkChildren) {
                       if($checkChildren->parrent_id==$item->id)
                       {
                           $result.="<span class=\"caret\"></span>";
                           break;
                       }

                    }

                     $result.="</a>";
                }
                else
                {
                    if($item->url!=""){
                        $result.= "<a ".($item->target==1?"target='_blank'":"")." rel=\"".($item->target==2?Html::entities($item->url):"")."\" href=\"".Html::entities($item->url)."\" class=\"".($item->target==2?"load-modal":"")."\">".Html::entities($item->title)."</a>";
                    }
                    else{
                        $result.= "<a ".($item->target==1?"target='_blank'":"")." rel=\"".($item->target==2?Html::entities($item->url):"")."\" href=\"/".Html::entities($item->slug)."\" class=\"".($item->target==2?"load-modal":"")."\">".Html::entities($item->title)."</a>";
                    }

                }

                $result .= self::buildMenu($menu, $item->id) . "</li>";
            }
        if($result){
            if($parrent_id==0)
            {

                $search="";
                $linkuser="";

                if(!Auth::guard('frontend')->check())
                {
                    // $linkuser="<li><a href=\"/login\" class=\"c-btn-border-opacity-04 c-btn btn-no-focus c-btn-header btn btn-sm c-btn-border-1x c-btn-dark c-btn-circle c-btn-uppercase c-btn-sbold\">
                    // <i class=\"icon-user\"></i> Đăng nhập</a>
                    // </li>";
                    // $linkuser.="<li><a href=\"/register\" class=\"c-btn-border-opacity-04 c-btn btn-no-focus c-btn-header btn btn-sm c-btn-border-1x c-btn-dark c-btn-circle c-btn-uppercase c-btn-sbold\">
                    // <i class=\"icon-key icons\"></i> Đăng ký</a>
                    // </li>";
                }
                else{
                    // $linkuser="<li>
                    // <a href=\"/user/profile\" title=\"".Auth::guard('frontend')->user()->username."\" class=\"c-btn-border-opacity-04 c-btn btn-no-focus c-btn-header btn btn-sm c-btn-border-1x c-btn-dark c-btn-circle c-btn-uppercase c-btn-sbold\">
                    //     <i class=\"icon-user\"></i> ".Html::entities(str_limit(Auth::guard('frontend')->user()->username,11,"..."))." - $ ".number_format(Auth::guard('frontend')->user()->balance). "</a> </li>";

                    // $linkuser.="<li>
                    //     <a href=\"/logout\" class=\"c-btn-border-opacity-04 c-btn btn-no-focus c-btn-header btn btn-sm c-btn-border-1x c-btn-dark c-btn-circle c-btn-uppercase c-btn-sbold\">
                    //         Đăng xuất
                    //     </a>
                    // </li>";
                }
                return "\n<ul  class=\"nav navbar-nav c-theme-nav\">\n$result $search $linkuser</ul>\n";
            }
            else{
                return "\n<ul id=\"children-of-".$parrent_id."\" class=\"c-menu-type-classic c-pull-left \" >\n$result</ul>\n";
            }
        }
        else{
            return null;
        }

//        return $result ? "\n<ul id=\"children-of-".$parrent_id."\" class=\"list-unstyled collapse  children-of-".$parrent_id."\">\n$result</ul>\n" : null;
    }

    public static function buildLogUser()
    {
        $result = null;
        $linkuser="";

        if(!Auth::guard('frontend')->check())
        {
            // $linkuser.="<ul class=\"sa-login clearfix\">
            //     <li>
            //         <a href=\"https://nhapnick.com/shopmeowdgame_vn\" title=\"Đăng Nhập\">Đăng Nhập</a></li>
            //     </li>
            // </ul>";

            $linkuser.="<ul class=\"sa-login clearfix\" style=\"margin-left: 10px\">
                <li>
                    <a href=\"/register\" title=\"Đăng ký\">Đăng ký</a></li>
                </li>
            </ul>";
            $linkuser.="<ul class=\"sa-login clearfix\" >
                <li>
                    <a href=\"/login\" title=\"Đăng Nhập\">Đăng Nhập</a></li>
                </li>
            </ul>";
        }
        else{



            $linkuser.="<ul class=\"sa-login clearfix\" style=\"margin-left: 10px\">
                <li>
                    <a href=\"/logout\" title=\"Đăng xuất\">Đăng xuất</a></li>
                </li>
            </ul>";
            $linkuser.="<ul class=\"sa-login clearfix\">
                <li>
                    <a href=\"/user/profile\" title=\"".Html::entities(str_limit(Auth::guard('frontend')->user()->fullname==''?str_replace(\Request::gethost().'_','',Auth::guard('frontend')->user()->username):Auth::guard('frontend')->user()->fullname,11,"..."))."\"> ".Html::entities(str_limit(Auth::guard('frontend')->user()->fullname==''?str_replace(\Request::gethost().'_','',Auth::guard('frontend')->user()->username):Auth::guard('frontend')->user()->fullname,11,"..."))." - $ ".number_format(Auth::guard('frontend')->user()->balance). "</a></li>
                </li>
            </ul>";
        }
        return $linkuser;
    }


}
