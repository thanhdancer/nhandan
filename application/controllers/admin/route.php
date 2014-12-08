<?php
/**
 * Created by PhpStorm.
 * User: lion
 * Date: 12/6/14
 * Time: 8:28 AM
 */


if (! defined('BASEPATH')){
    exit("You cannot access this file directly.");
}

class Route extends CI_Controller {
    private $_user;
    private $data;

    /**
     * Constructor
     */
    public function __construct(){
        parent::__construct();
        $this->load->library('session');
        $this->load->helper(array('url', 'language', 'form', 'file'));

        // check authenication
        $this->_user = $this->session->all_userdata();

    	if(!$this->user->authentication()){
        	redirect('admin/user/login');
        }


        $this->data = array();
        $this->data['title'] = "Administrator Control Panel - Modules | ";
        $this->data['breadcrumb'] = array(
            array(
                'name'      =>  'Home',
                'icon'      =>  'home',
                'link'      =>  site_url('admin')
            ),
            array(
                'name'      =>  'Modules',
                'link'      =>  site_url('/modules/')
            )
        );

        $this->load->model('route/Route_model', 'rModel');
    }

    public function index(){
        $this->data['pageTitle'] = "Route list";
        $this->data['modules'] = $this->rModel->getAll();

        $this->data['_additionFooter'] = '
            <link rel="stylesheet" href="' . base_url() .'assets/js/zurb-responsive-tables/responsive-tables.css">
            <link rel="stylesheet" href="' . base_url() . 'assets/js/selectboxit/jquery.selectBoxIt.css">
            <script src="' . base_url() . 'assets/js/zurb-responsive-tables/responsive-tables.js"></script>
            <script src="' . base_url() . 'assets/js/selectboxit/jquery.selectBoxIt.min.js"></script>
            <script src="' . base_url() . 'assets/js/bootstrap-switch.min.js"></script>
        ';

        $this->data['_mainModule'] = $this->load->view('route/list.phtml', $this->data, TRUE);

        if($this->session->flashdata('message')){
            $this->data['_additionFooter'] .= '
                <script type="text/javascript">
                    jQuery(document).ready(function($){
                        toastr.' . $this->session->flashdata('type') . '(\'' . $this->session->flashdata('message') . '\')
                    });
                </script>
            ';
        }

        $this->load->view('includes/_adminTemplate.phtml', $this->data);

    }


} 