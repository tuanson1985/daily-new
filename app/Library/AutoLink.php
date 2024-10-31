<?php


namespace App\Library;

use function PHPUnit\Framework\isInstanceOf;
use function PHPUnit\Framework\matches;

class AutoLink
{

    public static function replace_old($data = '')
    {
        $config = json_decode(setting('sys_theme_auto_link'));
        if (count($config)) {

//            foreach ($config as $keyword) {
//
//                /*target = 1 (mở tab mới)*/
//                $target = $keyword->target == 1 ? 'target="_blank"' : '';
//                $percent_dofollow = 0;
//                if ($keyword->dofollow == 1) {
//                    $percent_dofollow = $keyword->percent_dofollow;
//                }
//                /*Xử lý các thẻ a có sẵn trước khi autolink*/
//
//                $tags_a = self::getTagsA($data, $keyword->title);
//                foreach ($tags_a['title'] as $key => $title) {
//                    /*Nếu mà content của thẻ a có nội dung giống như keyword thì replace xoá thẻ a thay bằng text*/
//                    if (strtolower($title) == strtolower($keyword->title)) {
//                        $data = preg_replace('/' . str_replace('/', '\/', $tags_a[0][$key]) . '/', $title, $data);
//                    }
//                }
//
//                /*Xử lý các từ khoá được tìm thấy nhưng không nằm trong thẻ a*/
//                $reg_not_tag_a = "/($keyword->title)(?![^<a]*>|[^<>]*<\/a>)/i";
//
////               $count_match = 0;
////               if ($keyword->dofollow == 1){
////                   preg_match_all($reg_not_tag_a,$data,$count_not_a);
////                   $count_not_a = count($count_not_a[0]);
////                   $count_match = round( $count_not_a - ($count_not_a * $percent_dofollow/100) );
////               }
//
//                /*$keyword->link_type = 1 (Internal Link) = 2 (External Link)*/
//
//                if ($keyword->link_type == 1) {
//                    $internal_link = $keyword->url;
//
//                    if ($keyword->dofollow == 1) {
//                        $tag_a = '<a href="' . $internal_link . '" ' . $target . ' rel="follow">$1</a>';
//                        $data = preg_replace($reg_not_tag_a, $tag_a, $data, 1);
//
//                    } else if ($keyword->dofollow == 0) {
//                        $tag_a = '<a href="' . $internal_link . '" ' . $target . ' rel="nofollow">$1</a>';
//                        $data = preg_replace($reg_not_tag_a, $tag_a, $data);
//                    }
//                }
//
//                if ($keyword->link_type == 2) {
//                    $urls = json_decode($keyword->params_access);
//                    $count_urls = count($urls);
//                    if ($keyword->dofollow == 1) {
//                        if ($count_urls == 1) {
//                            $new_a = '<a href="' . $urls[0] . '" ' . $target . ' rel="follow">$1</a>';
//                            $data = preg_replace($reg_not_tag_a, $new_a, $data, 3);
//                        }
//                    }
//                    if ($count_urls == 2) {
//                        $new_a = '<a href="' . $urls[0] . '" ' . $target . '>$1</a>';
//                        $data = preg_replace($reg_not_tag_a, $new_a, $data, 3);
//                        $reg_old_a = '/' . str_replace('/', '\/', $new_a) . '/';
//                        $new_a = '<a href="' . $urls[1] . '" ' . $target . '>$1</a>';
//                        $data = preg_replace($reg_old_a, $new_a, $data, 1);
//                    }
//                    if ($count_urls >= 3) {
//                        $new_a = '<a href="' . $urls[0] . '" ' . $target . '>$1</a>';
//                        $data = preg_replace($reg_not_tag_a, $new_a, $data, 1);
//                        $new_a = '<a href="' . $urls[1] . '" ' . $target . '>$1</a>';
//                        $data = preg_replace($reg_not_tag_a, $new_a, $data, 1);
//                        $new_a = '<a href="' . $urls[2] . '" ' . $target . '>$1</a>';
//                        $data = preg_replace($reg_not_tag_a, $new_a, $data, 1);
//                    }
//                }
//            }
            foreach($config as $option){
               $data = self::replace($option->title,$option->url,$data,1,0);
            }
        }
        return $data;
    }

    public static function replace($keyword = '', $link = '/', $content = '', $target = 1, $follow = 0 ,$link_type = 1)
    {
        $changed = false;
        $attr_follow = $follow ? 'rel="follow"' : 'rel="nofollow"';

        $attr_target = $target == 1 ? 'target="_blank"' : '';

        $regex_not_tags_a = "/($keyword)(?![^<a]*>|[^<>]*<\/a>)(?![^<]*\/>)/i";


        $hyperlink = '<a href="' . $link . '" ' . $attr_target . ' ' . $attr_follow . '>$1</a>';

//        $old_tags_a = self::getTagsA($content, $keyword);

        /*Tìm xem có thẻ a nào có chứa từ khoá trùng với keyword config hay không */
//        if (count($old_tags_a[0])) {
//            $changed = true;
//            foreach ($old_tags_a[0] as $key => $old_tag_a) {
//                if (strtolower($keyword) == strtolower($old_tags_a['title'][$key])) {
//                    $content = preg_replace('/'.str_replace('/', '\/', $old_tag_a).'/', $hyperlink, $content, 1);
//                    break;
//                }
//            }
//        } else {
            preg_match_all($regex_not_tags_a,$content,$result_check);
            if (count($result_check[0])) {
                $changed = true;
            }
            /* Nếu như mà không tìm thấy thẻ a có sẵn nào trùng với keyword trong config thì bắt đầu tìm từ khoá text không nằm trong tag a để replace*/
            $content = preg_replace($regex_not_tags_a, $hyperlink, $content, 1);
//        }
        return [
            'content'=>$content,
            'changed' => $changed,
        ];
    }

    private static function getTagsA($html, $title)
    {

        $reg_tag_a = "/<a.*>(?<title>.+)<\/a>/U";

        preg_match_all($reg_tag_a, $html, $matches);
        foreach ($matches['title'] as $key => $keyword) {
            if (strtolower($keyword) != strtolower($title)) {
                unset($matches[0][$key]);
                unset($matches[1][$key]);
                unset($matches['title'][$key]);
            }
        }
        return $matches;
    }
}
