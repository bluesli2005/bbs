<?php
/*
+--------------------------------------------------------------------------
|   WeCenter [#RELEASE_VERSION#]
|   ========================================
|   by WeCenter Software
|   © 2011 - 2014 WeCenter. All Rights Reserved
|   http://www.wecenter.com
|   ========================================
|   Support: WeCenter@qq.com
|
+---------------------------------------------------------------------------
*/

if (! defined('IN_ANWSION'))
{
	die();
}

class plugin extends AWS_ADMIN_CONTROLLER
{
	public function setup(){
		TPL::assign('menu_list', $this->fetch_menu_list());
	}
	public function index_action(){
		$addons = $this->model('plugin')->getList();
        $upcount=$this->model('plugin')->get_new_plugin();
        $uparr=['ad'=>'1.0.1','consult'=>'1.1.1'];
        TPL::assign('uparr', $uparr);
        TPL::assign('list', $addons);
		TPL::assign('upcount', $upcount);
		TPL::output('admin/plugin/index');
	}


    public function install_action(){
        $addon_name     =   trim($_POST['addon_name']);
        $addon_dir     =   $this->model('plugin')->get_plugin_dir(trim($_POST['addon_name']));
        $class=ADDON_PATH . $addon_dir.'/'.$addon_name.'.php';
        if(file_exists($class)){
        require_once($class);

        if(!class_exists($addon_name))
     		   H::ajax_json_output(AWS_APP::RSM(null, -1, '插件不存在或者已经损坏'));
        }
        $cls=new $addon_name;
        $ret=$cls->install($addon_name);
        if($ret){
     		   H::ajax_json_output(AWS_APP::RSM(null, 1, null));
     	}


    }
    public function uninstall_action(){
        $addon_name     =   trim($_POST['addon_name']);
        $addon_dir     =   $this->model('plugin')->get_plugin_dir(trim($_POST['addon_name']));

        $class=ADDON_PATH . $addon_dir.'/'.$addon_name.'.php';
        if(file_exists($class)){
        require_once($class);
        if(!class_exists($addon_name))
     		   H::ajax_json_output(AWS_APP::RSM(null, -1, '插件不存在或者已经损坏'));
        }
        $cls=new $addon_name;
        $ret=$cls->uninstall($addon_name);
        if($ret){
     		   H::ajax_json_output(AWS_APP::RSM(null, 1, null));
     	}
    }
    public function enable_action(){
        $addon_name     =   trim($_POST['addon_name']);
        $addon_dir     =   $this->model('plugin')->get_plugin_dir(trim($_POST['addon_name']));

        $class=ADDON_PATH . $addon_dir.'/'.$addon_name.'.php';
        if(file_exists($class)){
        require_once($class);
        if(!class_exists($addon_name))
     		   H::ajax_json_output(AWS_APP::RSM(null, -1, '插件不存在或者已经损坏'));
        }
        $cls=new $addon_name;
        $ret=$cls->enable($addon_name);
        if($ret){
     		   H::ajax_json_output(AWS_APP::RSM(null, 1, null));
     	}
    }
    public function disable_action(){
        $addon_name     =   trim($_POST['addon_name']);
        $addon_dir     =   $this->model('plugin')->get_plugin_dir(trim($_POST['addon_name']));
        
        $class=ADDON_PATH . $addon_dir.'/'.$addon_name.'.php';
        if(file_exists($class)){
        require_once($class);
        if(!class_exists($addon_name))
     		   H::ajax_json_output(AWS_APP::RSM(null, -1, '插件不存在或者已经损坏'));
        }
        $cls=new $addon_name;
        $ret=$cls->disable($addon_name);
        if($ret){
     		   H::ajax_json_output(AWS_APP::RSM(null, 1, null));
     	}
    }
    public function config_action(){
        $addon_name=trim($_GET['addon_name']);
        $info=$this->model('plugin')->get_info($addon_name);
        $config=json_decode($info['config'],true);
		TPL::assign('configs', $config);
		TPL::assign('info', $info);
		TPL::output('admin/plugin/config');
    }
    public function save_config_action(){
        $addon_name=trim($_POST['addon_name']);
        $config=$this->model('plugin')->get_info($addon_name,true);
    	$post=[];
        foreach ($_POST['config'] as $key => $value) {
            $config[$key]['value']=$value;
        }
        $this->model('plugin')->update('plugins',['config'=>json_encode($config,JSON_UNESCAPED_UNICODE)],'name="'.$addon_name.'"');
        H::ajax_json_output(AWS_APP::RSM(null, -1, '配置成功'));
    }

    public function save_tab_config_action(){
        $addon_name=trim($_POST['addon_name']);
        $config=ADDON_PATH . $addon_name.'/config.php';
        if(file_exists($config))
        $config=require($config);
        $post=[];
        foreach ($_POST['config'] as $key => $value) {
            foreach ($value as $k => $v) {
            $config['group'][$key]['config'][$k]['value']=$v;
            }
        }
       file_put_contents(ADDON_PATH . "{$addon_name}/config.php", "<?php\n return " . var_export($config,TRUE).';');
        H::ajax_json_output(AWS_APP::RSM(null, -1, '配置成功'));        
    }

    public function get_new_plugin_action(){
        $ret= $this->model('plugin')->get_new_plugin(2);
        if($ret)
        H::ajax_json_output(AWS_APP::RSM(null, 1, null));        

    }
    public function upgrade_action(){
        $addon_name     =   trim($_POST['addon_name']);
        $version     =   trim($_POST['version']);
        $addon_dir     =   $this->model('plugin')->get_plugin_dir(trim($_POST['addon_name']));
        $class=ADDON_PATH . $addon_dir.'/'.$addon_name.'.php';
        if(file_exists($class)){
        require_once($class);

        if(!class_exists($addon_name))
               H::ajax_json_output(AWS_APP::RSM(null, -1, '插件不存在或者已经损坏'));
        }
        $cls=new $addon_name;
        $ret=$this->model('plugin')->upgrade($addon_name,$version);
        if($ret){
               H::ajax_json_output(AWS_APP::RSM(null, 1, null));
        }
    }
     public function doact_action(){
        $name=trim($_GET['p']);
        $action=trim($_GET['a']);
        $data=$_POST?$_POST:$_GET;
        // var_dump($_GET);

        hook($name,$action,$data);
    }   
}