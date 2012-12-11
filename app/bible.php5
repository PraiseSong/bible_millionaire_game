<?php
/**
 * Created by JetBrains PhpStorm.
 * User: qizhuq
 * Date: 12/8/12
 * Time: 8:42 PM
 * To change this template use File | Settings | File Templates.
 */
if(isset($_GET['action']) && $action = $_GET['action']){
    include("../config.php5");
    include("db.php5");
}

class Bible{
    protected $db;
    private $tablename = 'crossbible';

    function __construct(){
        global $host,$db_user,$db_pass;

        $this->db = new DB('cross',$host,$db_user,$db_pass);
        $this->db->query("SET NAMES 'UTF8'");
    }

    public function queryBooktitle(){
        $data = '';
        $resultStatus = 101;
        $result = array();
        if(!$this->db){
            $result['resultStatus']=$resultStatus;
            $result['data']=$data;
        }else{
            $data = $this->db -> queryManyObject("SELECT distinct BookTitle,Book,Alias FROM `$this->tablename` ORDER BY ID");
            $result['resultStatus']=100;
            $result['data']=$data;
        }

        return json_encode($result);
    }

    public function queryArticleNum(){
        $id = isset($_GET['id']) ? $_GET['id']: null;
        $data = '';
        $resultStatus = 101;
        $result = array();
        if(!$this->db){
            $result['resultStatus']=$resultStatus;
            $result['data']=$data;
        }elseif(!$id){
            $result['resultStatus']=$resultStatus;
            $result['data']=$data;
            $result['memo']="缺少书卷ID";
        }else{
            $rows = $this->db->queryManyObject("SELECT Verse FROM `$this->tablename` where `Book`=$id");
            $article_nums = null;
            foreach($rows as $row){
                $article_num = $row->Verse;
                $article_num = preg_split('/\\:/',$article_num);
                $article_nums[] = $article_num[0];
            }
            $article_nums = array_unique($article_nums);

            $data = max($article_nums);
            $result['resultStatus']=100;
            $result['data']=$data;
        }

        return json_encode($result);
    }

    public function queryVerseNum(){
        $id = isset($_GET['id']) ? $_GET['id']: null;
        $article = isset($_GET['article']) ? $_GET['article']: null;
        $data = '';
        $resultStatus = 101;
        $result = array();

        if(!$this->db){
            $result['resultStatus']=$resultStatus;
            $result['data']=$data;
        }elseif(!$id){
            $result['resultStatus']=$resultStatus;
            $result['data']=$data;
            $result['memo']="缺少书卷ID";
        }elseif(!$article){
            $result['resultStatus']=$resultStatus;
            $result['data']=$data;
            $result['memo']="没有提供章节参数";
        }else{
            $verses_num = array();

            $rows = $this->db->queryManyObject("SELECT Verse FROM `$this->tablename` where `Book`=$id and `Verse` like '$article:%'");
            foreach($rows as $row){
                $verse_num = $row->Verse;
                $verse_num = preg_split('/\\:/',$verse_num);
                $verses_num[] = $verse_num[1];
            }
            $verses_num = array_unique($verses_num);

            $data = max($verses_num);
            $result['resultStatus']=100;
            $result['data']=$data;

            return json_encode($result);
        }
    }

    public function queryBible(){
        $id = isset($_GET['id']) ? $_GET['id']: null;
        $article = isset($_GET['article']) ? $_GET['article']: null;
        $data = '';
        $resultStatus = 101;
        $result = array();
        $text = '';
        $verse_start = isset($_GET['verse_start']) ? $_GET['verse_start']: null;
        $verse_stop = isset($_GET['verse_stop']) ? $_GET['verse_stop']: null;
        $article_text = '';

        if($verse_stop < $verse_start){
            $result['resultStatus']=$resultStatus;
            $result['data']=$data;
            $result['memo']="对不起，后面的节数不能小于前面的节数";
        }elseif(!$this->db){
            $result['resultStatus']=$resultStatus;
            $result['data']=$data;
        }elseif(!$id){
            $result['resultStatus']=$resultStatus;
            $result['data']=$data;
            $result['memo']="缺少书卷ID";
        }elseif(!$article){
            $result['resultStatus']=$resultStatus;
            $result['data']=$data;
            $result['memo']="没有提供章节参数";
        }elseif(!$verse_start || !$verse_stop){
            $result['memo']="没有提供章节参数";
        }else{
            if($verse_stop > $verse_start){
                for($i = $verse_start;$i<=$verse_stop;$i++){
                    $space_row = $this->db->queryManyObject("SELECT * FROM `$this->tablename` where `Book`=$id and `Verse`='$article:$i'");
                    if(empty($article_text)){
                        $article_text = $space_row[0]->BookTitle.$article.':'.$verse_start.'-'.$verse_stop.' ';
                        $text.=$article_text;
                    }
                    if($space_row[0]->TextData){
                        $text .= ($i === $verse_start ? '' : "<sup style=\"font-size:10px;\">$i</sup>").$space_row[0]->TextData;
                    }else{
                        $text .= "<span style=\"color:#ff0000;\"> 对不起，没有找到第<span>$i</span>节</span>";
                    }
                }
            }elseif($verse_stop === $verse_start){
                $row = $this->db->queryManyObject("SELECT * FROM `$this->tablename` where `Book`=$id and `Verse`='$article:$verse_start'");

                if($row[0]->TextData){
                    $text .= $row[0]->BookTitle.$row[0]->Verse.' '.$row[0]->TextData;
                }
            }


            $data = $text;
            $result['resultStatus']=100;
            $result['data']=$data;

            return json_encode($result);
        }
    }
}

if(isset($_GET['action']) && $action = $_GET['action']){
    $bible = new Bible();

    switch($action){
        case 'queryBooktitle':
            print $bible->queryBooktitle();
            break;
        case 'query_article_num':
            print $bible->queryArticleNum();
            break;
        case 'query_verse_num':
            print $bible->queryVerseNum();
            break;
        case 'query_bible':
            print $bible->queryBible();
            break;
        default:
            break;
    }
}
?>