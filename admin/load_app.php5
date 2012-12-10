<?php
/**
 * Created by JetBrains PhpStorm.
 * User: qizhuq
 * Date: 12/8/12
 * Time: 8:29 PM
 * To change this template use File | Settings | File Templates.
 */
if(is_dir(APP_PATH)){
    $app = opendir(APP_PATH);
    while($file = readdir($app)){
      $php_file = APP_PATH.'/'.$file;
      if(is_file($php_file)){
          include_once($php_file);
      }
    }
}
?>