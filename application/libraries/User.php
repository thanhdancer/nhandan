<?php
/**
 * Created by PhpStorm.
 * User: lion
 * Date: 12/6/14
 * Time: 8:36 AM
 */



if ( ! defined ('BASEPATH')) exit("You can access this file directly");


class User {
	
	private $CI;
	
	public function __construct(){
		$this->CI	=&	get_instance();
		$this->CI->load->library('session');
		$this->CI->load->model('permission/Permission_model', '_pModel');
	}
	
	public function authentication(){
		$tmp = $this->CI->session->all_userdata();
		
		// Check user logged in
		if(!isset($tmp['userid']) ){
			return false;
		}
		
		// Check user can user module
		$controller = $this->CI->router->class;		
		$method = $this->CI->router->method;
		
		$acceptableRoute = $this->CI->_pModel->getByUsergroup($tmp['group'], TRUE);
		
		$acceptRoute = array();
		foreach($acceptableRoute as $accept){
			$acceptRoute[] = $accept->route;
		}
		
		$route = $controller . '/' . $method;
		
		if(!in_array($tmp['userid'], $this->CI->config->config['superadmin']) && !in_array($route, $acceptRoute) ){
			return false;
		}
		
		return true;
	}
	
} 