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
            $data = $this->db -> queryManyObject("SELECT distinct BookTitle,Book,Alias FROM `$this->tablename` ORDER BY ID");
            $result['resultStatus']=$resultStatus;
        }else{
            $data = $this->db -> queryManyObject("SELECT distinct BookTitle,Book,Alias FROM `$this->tablename` ORDER BY ID");
            $result['resultStatus']=100;
            $result['data']=$data;
        }

        return json_encode($result);
    }
}

if(isset($_GET['action']) && $action = $_GET['action']){
    $bible = new Bible();

    switch($action){
        case 'queryBooktitle':
              print $bible->queryBooktitle();
            break;
        default:
            break;
    }
}
?>