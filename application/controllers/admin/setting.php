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
 * @version		$Id: setting.php  11/2/14 2:40 AM lion $
 */

class Setting extends CI_Controller{

    private $_user;
    private $data;

    /**
     * Contructor
     */

    public function __construct(){
        parent::__construct();
        $this->load->library('session');
        $this->load->helper(array('url', 'language', 'form', 'file'));

        $this->_user = $this->session->all_userdata();
        if(!isset($this->_user['userid'])){
            redirect('admin/user/login');
        }

        $this->data = array();
        $this->data['title'] = "Administrator Control Panel - Setting | ";
        $this->data['breadcrumb'] = array(
            array(
                'name'      =>  'Home',
                'icon'      =>  'home',
                'link'      =>  site_url('admin')
            ),
            array(
                'name'      =>  'Setting',
                'link'      =>  site_url('/setting/')
            )
        );

        $this->load->model('setting/Setting_model', 'sModel');
    }

    /**
     * Index
     */
    public function index(){
        $data = array();
        $data['cfg'] = array();
        foreach($this->sModel->moduleConfig('core') as $cfg){
            $data['cfg'][$cfg->configname] = $cfg->configvalue;
        }

        $data['pageTitle'] = "Settings";

        $this->data['_additionFooter'] = '
            <link rel="stylesheet" href="' . base_url() .'assets/js/zurb-responsive-tables/responsive-tables.css">
            <link rel="stylesheet" href="' . base_url() . 'assets/js/selectboxit/jquery.selectBoxIt.css">
            <script src="' . base_url() . 'assets/js/zurb-responsive-tables/responsive-tables.js"></script>
            <script src="' . base_url() . 'assets/js/selectboxit/jquery.selectBoxIt.min.js"></script>
            <script src="' . base_url() . 'assets/js/bootstrap-switch.min.js"></script>
        ';
        $this->data['_mainModule'] = $this->load->view('setting/index.phtml', $data, TRUE);

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

    /**
     * Update setting
     */
    public function update(){
        $data = array();
        $data['module'] = "core";
        $data['userid'] = $this->_user['userid'];

        // set config site title
        $this->sModel->setConfig(array_merge($data, [
            'name' => 'siteTitle',
            'value' => $this->input->post('siteTitle', TRUE)
        ]));

        // set config logo link
        $this->sModel->setConfig(array_merge($data, [
            'name' => 'logo',
            'value' => $this->input->post('logo', TRUE)
        ]));

        $this->session->set_flashdata(array(
            'type'      => 'success',
            'message'   => 'Update setting successful'
        ));

        redirect('admin/setting/index');
    }

}

 ?>
 