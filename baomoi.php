<?php
require_once 'c-config.php';
if(!isset($_GET['id']))
    return 'stop';

$id=$_GET['id'];
$page=$_GET['page'];
$table='baomoi';
if($cdb->get_var($cdb->prepare("select * from $table where original_id=%s",$id))){
    save_state($page, $id);
    echo '<li style="color:yellow">'.$id.' exists</li>';
    die;
}

$link=sprintf('http://www.baomoi.com/!/59/%s.epi',$id);
@$data=file_get_contents($link);
if(!$data){
    function valid($id){
        $link=sprintf('http://www.baomoi.com/!/59/%s.epi',$id);
        //echo $link;
        $headers=get_headers($link);
        if(!$headers)
            return true;
        $firstline=$headers[0];
        $statusCode=substr($firstline, 9, 3);
        $is_error=$statusCode>=300;
        return !$is_error;
    }
    if(valid($id)){//valid id but network error should try again
        die('error');
    }else{
        die('<li style="color:red">'.$id.' was removed</li>');
    }
}
$html=str_get_html($data);
if($html){
    $article=$html->find('.article-body',0);
    if($article){
        $article_header=$article->find('.story-header',0);
        if($article_header){
            $title=$article->find(' h1.title',0);
            if($title){
                $title=$title->innertext();
            }
            $time=$article->find('.story-header span.time',0);
            if($time){
                $time=trim($time->innertext());
                if($time){
                    $parts=explode(' ',$time);
                    $date=$parts[0];

                    $date_parts=explode('/',$date);
                    $date_parts=array_reverse($date_parts);
                    $timestamp=strtotime(join('-',$date_parts).$parts[1]);
                    $time=date('Y-m-d H:i:s',$timestamp);
                }
            }
            $link=$article_header->find('ul.interacts li',2);
            if($a=$link->find('a',0)){
                $url=$a->href;
            }
        }
        $article_body=$article->find('.story-body',0);
        if($article_body){
            $description=$article_body->find('.summary',0);
            if($description){
                $description=$description->innertext();
            }
            $articleText=$article_body->find('div[itemprop=articleBody]',0);
            if($articleText){
                $articleText=$articleText->innertext();
            }
        }
        $keywords=$article->find('.article-relatives .keywords .itemlisting li');
        $tags=array();
        if($keywords){
            foreach($keywords as $li){
                if($a=$li->find('a',0)){
                    $tags[]=trim($a->innertext());
                }
            }
        }

        $data=array(
            'original_id'=>$id,
            'url'=>$url,
            'title'=>$title,
            'time'=>$time,
            'description'=>$description,
            'content'=>$articleText,
            'tags'=>join(', ',$tags)

        );
        if($title&&$description&&$url&&$articleText){
            if($cdb->insert($table, $data)){
                echo '<li style="color:green">'.$id.'- success </li>';
                save_state($page, $id);
                return;
            }else{
                echo 'error';
                return;
            }
        }
    }
}
save_state($page, $id);
echo '<li style="color:red">'.$id.' not found</li>';