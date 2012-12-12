<?php
/**
 * Created by JetBrains PhpStorm.
 * User: qizhuq
 * Date: 12/11/12
 * Time: 11:38 PM
 * To change this template use File | Settings | File Templates.
 */
if(isset($_GET['action']) && $action = $_GET['action']){
    include("../config.php5");
    include("db.php5");
    $topics_tablename = 'topics';
    $subjects_tablename = 'subjects';
    $db = new DB('bible_millionaire_game',$host,$db_user,$db_pass);
    $db->query("SET NAMES 'UTF8'");
}

if(isset($_GET['action'])){
    //提交游戏主题
    function submitTopic(){
        global $db,$topics_tablename;
        $action = $_GET['action'];
        $topic = $_GET['topic'];
        $topic_parent = $_GET['topic_parent'];

        if(!$topic){
            $data = array('resultStatus'=>101,'memo' => "请输入主题内容");
            return json_encode($data);
        }

        $exsit = $db->queryUniqueObject("select * from $topics_tablename where content='$topic'");

        if($exsit){
            $data = array('resultStatus'=>101,'memo' => "该主题已经存在");
            return json_encode($data);
        }

        $sql = "insert into $topics_tablename (content,parent) values('$topic','$topic_parent')";

        if(!$topic_parent){
            $sql = "insert into $topics_tablename (content) values('$topic')";
        }

        $db->query($sql);

        if($db->lastInsertedId()){
            $data = array('resultStatus'=>100);
            return json_encode($data);
        }else{
            $data = array('resultStatus'=>101);
            return json_encode($data);
        }
    }

    //查询所有游戏主题
    function queryTopics(){
        global $db,$topics_tablename;
        $row = $db->queryManyObject("select * from $topics_tablename");

        if($row){
            $result = $row;
            $data = array('resultStatus'=>100,'data'=>$result);
            return json_encode($data);
        }else{
            $data = array('resultStatus'=>101);
            return json_encode($data);
        }
    }

    //提交题目
    function submitSubject(){
        global $db,$subjects_tablename;
        $content = $_GET['reference'];
        $reference = $_GET['reference'];
        $topics = $_GET['topics'];
        $time = $_GET['time'];

        $sql = "insert into $subjects_tablename (content,reference,topics,time) values('$content','$reference','$topics','$time')";

        $db->query($sql);

        if($db->lastInsertedId()){
            $data = array('resultStatus'=>100);
            return json_encode($data);
        }else{
            $data = array('resultStatus'=>101);
            return json_encode($data);
        }
    }

    switch($_GET['action']){
        case 'submit_topic':
            echo submitTopic();
            break;
        case 'query_topic':
            echo queryTopics();
            break;
        case 'submit_subject':
            echo submitSubject();
            break;
        default:
            break;
    }
}
?>