<?php include_once("../admin/config.php5"); ?>
<?php include_once("db.php5"); ?>
<?php include_once('../admin/'.TEMPLATES_PATH."/header.php5"); ?>

<?php
 if(isset($_FILES['file'])):
?>

<?php
     $data = array();

     $db = new DB('bible_millionaire_game',$host,$db_user,$db_pass);
     $db->query("SET NAMES 'UTF8'");

     function add_person( $content, $reference, $time, $topics,$right_solution,$solutions,$creator )
     {
         global $data, $db;

         $timestamp = date("Y-m-d H:i:s",time()+8*60*60);
         $status = '<b style="color:red;">失败</b>';

         $exist = $db->queryUniqueObject("select * from subjects where content='$content'");

         if($exist){
             $status = '<b style="color:red;">题目已经存在</b>';
         }else{
             $sql = "insert into subjects (content,reference,topics,time,right_solution,solutions,timestamp,creator) values('$content','$reference','$topics','$time','$right_solution','$solutions','$timestamp','$creator')";

             $db->query($sql);
         }

         if($db->lastInsertedId()){
             $status = '<b style="color:green;">成功</b>';
         }

         $data []= array(
             'content' => $content,
             'reference' => $reference,
             'time' => $time,
             'topics' => $topics,
             'right_solution' => $right_solution,
             'solutions' => $solutions,
             'creator' => $creator,
             'timestamp' => $timestamp,
             'status' => $status
         );
     }

     if ( $_FILES['file']['tmp_name'] )
     {
         $dom = DOMDocument::load( $_FILES['file']['tmp_name'] );

         $rows = $dom->getElementsByTagName( 'Row' );
         $first_row = true;
         foreach ($rows as $row)
         {
             if ( !$first_row )
             {
                 $content = "";
                 $reference = "";
                 $time = "";
                 $topics = "";
                 $right_solution = "";
                 $solutions = "";
                 $creator = "";

                 $index = 1;
                 $cells = $row->getElementsByTagName( 'Cell' );
                 foreach( $cells as $cell )
                 {
                     $ind = $cell->getAttribute( 'Index' );
                     if ( $ind != null ) $index = $ind;

                     if ( $index == 1 ) $content = $cell->nodeValue;
                     if ( $index == 2 ) $reference = $cell->nodeValue;
                     if ( $index == 3 ) $time = $cell->nodeValue;
                     if ( $index == 4 ) $topics = $cell->nodeValue;
                     if ( $index == 5 ) $right_solution = $cell->nodeValue;
                     if ( $index == 6 ) $solutions = $cell->nodeValue;
                     if ( $index == 7 ) $creator = $cell->nodeValue;

                     $index += 1;
                 }
                 add_person( $content, $reference, $time, $topics,$right_solution,$solutions,$creator );
             }
             $first_row = false;
         }
     }
?>
 <h3>你提交的数据如下：</h3>
 <table width="100%" style="border:1px solid #ddd;text-align:left;line-height:40px;" cellspacing="0" cellpadding="0">
     <tr style="background:#f2f2f2;border-bottom:1px solid #ddd;line-height:40px;">
         <th>内容</th>
         <th>引用的经文</th>
         <th>所需的时间</th>
         <th>所属的游戏主题</th>
         <th>可选答案</th>
         <th>正确答案</th>
         <th>创建人</th>
         <th>创建时间</th>
         <th>当前状态</th>
     </tr>
     <?php foreach( $data as $row ) { ?>
     <tr>
         <td><?php echo( $row['content'] ); ?></td>
         <td><?php echo( $row['reference'] ); ?></td>
         <td><?php echo( $row['time'] ); ?></td>
         <td><?php echo( $row['topics'] ); ?></td>
         <td><?php echo( $row['solutions'] ); ?></td>
         <td><?php echo( $row['right_solution'] ); ?></td>
         <td><?php echo( $row['creator'] ); ?></td>
         <td><?php echo( $row['timestamp'] ); ?></td>
         <td><?php echo $row['status']; ?></td>
     </tr>
     <?php } ?>
 </table>

<?php
   endif;
?>

<?php include_once('../admin/'.TEMPLATES_PATH."/footer.php5"); ?>