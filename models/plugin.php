<?php

if (!defined('IN_ANWSION')) {
    die;
}
class plugin_class extends AWS_MODEL {
   /**
     * 获取插件列表
     * @param string $addon_dir
     */
    public  function getList($addon_dir = '') {
        if (!$addon_dir) $addon_dir = ADDON_PATH;
        $dirs = array_map('basename', glob($addon_dir . '*', GLOB_ONLYDIR));
        
        if ($dirs === FALSE || !file_exists($addon_dir)) {
            $this->error = '插件目录不可读或者不存在';
            return FALSE;
        }

        $addons=$this->fetch_all('plugins');
        foreach ($addons as $key => $value) {
          $adddir     =   $this->get_plugin_dir($value['name']);
          if(!is_dir(ADDON_PATH . "{$adddir}")){
            unset($addons[$key]);
            continue;
          }
          $info=require_once(ADDON_PATH . "{$adddir}/config.php");
          $version=str_replace('.','',$info['version']);
          $db_version=str_replace('.', '',$value['version']);
          if($version>$db_version){
            $addons[$key]['upgrade']=true;
            $addons[$key]['up_version']=$info['version'];
          }
          else{
            $addons[$key]['up_version']=false;
          }
        }
        return $addons;
    }
    public function install($addon_name){
        $addon_dir     =   $this->get_plugin_dir($addon_name);
         $_sql= file_get_contents(ADDON_PATH . "{$addon_dir}/install.sql");
         if($_sql){
             $sql= explode(";\r", str_replace(array('[#DB_PREFIX#]', "\n"), array($this->get_prefix(), "\r"), $_sql));
            foreach (array_filter($sql) as $_value) {
              if(!empty(trim($_value)))
                $this->query($_value.';');
            }
         }
         $this->update('plugins',['state'=>1],'name="'.$addon_name.'"');
       return true;
    }
    public function uninstall($addon_name){
        $addon_dir     =   $this->get_plugin_dir($addon_name);
         $_sql= file_get_contents(ADDON_PATH . "{$addon_dir}/uninstall.sql");
         if($_sql){
             $sql= explode(";\r", str_replace(array('[#DB_PREFIX#]', "\n"), array($this->get_prefix(), "\r"), $_sql));
            foreach ($sql as $_value) {
              if(!empty(trim($_value)))
                $this->query($_value.';');
            }
         }
         $this->update('plugins',['state'=>2],'name="'.$addon_name.'"');

       return true;
    }
    public function enable($addon_name){
         $this->update('plugins',['state'=>1],'name="'.$addon_name.'"');
       return true;
    }
    public function disable($addon_name){
         $this->update('plugins',['state'=>0],'name="'.$addon_name.'"');
       return true;
    }
    public function upgrade($addon_name,$version){
        $addon_dir     =   $this->get_plugin_dir($addon_name);
         $_sql= file_get_contents(ADDON_PATH . "{$addon_dir}/upgrade.sql");
         if($_sql){
             $sql= explode(";\r", str_replace(array('[#DB_PREFIX#]', "\n"), array($this->get_prefix(), "\r"), $_sql));
            foreach (array_filter($sql) as $_value) {
              if(!empty(trim($_value)))
                $this->query($_value.';');
            }
         }
         $this->update('plugins',['state'=>1,'version'=>$version],'name="'.$addon_name.'"');
       return true;
    }
/*新增内容*/
    public function get_info($plugin,$isconfig=false){
      $info=$this->fetch_row('plugins','name="'.$plugin.'"');
      if($isconfig)
        return json_decode($info['config'],true);
      else
        return $info;
    }
    public function get_new_plugin($type=1){
      $plugins=$this->query_all('select GROUP_CONCAT("wc_",name) as name from '.$this->get_table('plugins') );
      $plugins=explode(',',$plugins[0]['name']);
      // array_push($plugins, 'aws_external','aws_offical_external');
        if (!$addon_dir) $addon_dir = ADDON_PATH;
        $dirs = array_map('basename', glob($addon_dir . '*', GLOB_ONLYDIR));
        if ($dirs === FALSE || !file_exists($addon_dir)) {
            $this->error = '插件目录不可读或者不存在';
            return FALSE;
        }
        $count=0;
        $addons = array();
        $ndirs = "'" . implode("','", $dirs) . "'";
        $ddirs = array_flip($dirs);
        $ddirs = array_flip($ddirs);
        foreach ($ddirs as $key => $value) {
            if(!in_array($value,$plugins) and strstr($value, 'wc_')){
              $count+=1;
              if($type!=1){
              $config= include(ADDON_PATH . "{$value}/config.php");
              $config['config']=json_encode($config['config'],JSON_UNESCAPED_UNICODE);
              $this->insert('plugins',$config);
              }
            }
        }
        return $type==1?$count:true;
    }

    public function get_plugin_dir($name){
      return 'wc_'.$name;
    }
}

