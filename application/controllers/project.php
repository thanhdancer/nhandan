<?php
/**
 * LICENSE
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE Version 2 
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-2.0.txt
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@vv0lll.com so we can send you a copy immediately.
 * 
 * @license		http://www.gnu.org/licenses/gpl-2.0.txt GNU GENERAL PUBLIC LICENSE Version 2
 * @author		Thanh Dancer - dancer.thanh@gmail.com
 * @since			1.0
 * @version		$Id: project.php  10/30/14 5:05 PM lion $
 */

if(!defined('BASEPATH')){
    exit('You cannot access this file directly.');
}

class Project extends CI_Controller{

    private $_user;
    private $data;
    public function __construct(){
        parent::__construct();
        $this->load->library('session');
        $this->load->helper(array('language', 'url', 'form', 'file'));

        $this->_user = $this->session->all_userdata();
        if(!isset($this->_user['userid']) && substr($this->router->fetch_method(),0,5) == 'admin' ){
            redirect('/user/login');
        }

        $this->data = array();
        $this->data['title'] = "Administrator Control Panel - Project | ";
        $this->data['breadcrumb'] = array(
            array(
                'name'      =>  'Home',
                'icon'      =>  'home',
                'link'      =>  site_url('admin')
            ),
            array(
                'name'      =>  'Project',
                'link'      =>  site_url('/project/adminMain')
            )
        );

        $this->load->model('project/Category_model', 'catModel');
        $this->load->model('project/Project_model', 'projectModel');
    }

    public function index(){

    }

    public function adminMain(){
        $this->load->view('includes/_adminTemplate.phtml', $this->data);
    }

    public function adminConfig(){

        $data = array();

        $data['pageTitle'] = "Config module <strong>Project</strong>";


        $this->data['_mainModule'] = $this->load->view('project/config.phtml', $data, TRUE);
        $this->load->view('includes/_adminTemplate.phtml', $this->data);
    }
}
 ?>
 