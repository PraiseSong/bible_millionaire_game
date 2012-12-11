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
    $tablename = 'topic';
    $db = new DB('bible_millionaire_game',$host,$db_user,$db_pass);
    $db->query("SET NAMES 'UTF8'");
}

if(isset($_GET['action'])){
    $action = $_GET['action'];
    $topic = $_GET['topic'];
    $topic_parent = $_GET['topic_parent'];

    $db->query("insert into $tablename (content,parent) values('$topic','$topic_parent')");

    if($db->lastInsertedId()){
        $data = array('resultStatus'=>100);
        echo json_encode($data);
    }else{
        $data = array('resultStatus'=>101);
        echo json_encode($data);
    }
}
?>