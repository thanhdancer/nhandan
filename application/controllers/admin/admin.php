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
 * @version		$Id: admin.php  10/18/14 1:24 AM lion $
 */

if(!defined('BASEPATH')){
    exit("Wrong way");
}

class Admin extends CI_Controller{

    private $data;
    /**
     *  Constructor
     */
    public function __construct() {
        parent::__construct();
        $this->load->library('session');
        $this->load->helper(array('url','language', 'form', 'file'));

        $logged = $this->session->userdata('userid');
        if ( !$logged ){
            redirect('admin/user/login');
        }

        $this->data = array();
        $this->data['title'] = "Administrator Control Panel - Dashboard | ";
        $this->data['breadcrumb'] = array(
            array(
                'name'      =>  'Home',
                'icon'      =>  'home',
                'link'      =>  site_url('admin')
            ),
            array(
                'name'      =>  'Dashboard',
                'link'      =>  site_url('/index/')
            )
        );
    }

    public function index(){

        $data = array();
        $data['pageTitle'] = "Dashboard";

        $this->load->model('project/Project_model', 'projectModel');
        $this->load->model('User_model', 'userModel');
        $this->load->model('article/Article_model', 'articleModel');
        
        $data['projects']   =   $this->projectModel->getAll('1', 'created_date', 0, 10000);
        $data['map']	    =	$this->projectModel->getGroupLocation();
        $data['users']      =   $this->userModel->getAll('created_date', 0, 10000);
        $data['article']    =   $this->articleModel->getAll();
        
        $data['_additionFooter'] = '
        <script type="text/javascript" src="'. base_url() . 'assets/js/fushioncharts/js/fusioncharts.js"></script>
		<script type="text/javascript" src="'. base_url() . 'assets/js/fushioncharts/js/themes/fusioncharts.theme.fint.js"></script>
		';
        
        $this->data['_mainModule'] = $this->load->view('admin/index.phtml', $data, TRUE);
        $this->load->view('includes/_adminTemplate.phtml', $this->data);



    }
}

 