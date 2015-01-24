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
 * @version		$Id: sponsor.php  1/24/15 11:51 AM lion $
 */
 
if(!defined('BASEPATH')){
    exit("You cannot access this file directly");
}


class Sponsor extends CI_Controller{

    private $_user;
    private $data;

    /**
     *
     */
    public function __construct(){
        parent::__construct();

        $this->load->library(array('session', 'user'));
        $this->load->helper(array('language', 'url', 'form', 'text'));

        $this->_user = $this->session->all_userdata();

        if(!$this->user->authentication()){
            redirect('admin/user/login');
        }

        $this->data = array();

        $this->data['title'] = "Administrator Control Panel - Sponsor | ";
        $this->data['breadcrumb'] = array(
            array(
                'name'      =>  'Home',
                'icon'      =>  'home',
                'link'      =>  site_url('admin')
            ),
            array(
                'name'      =>  'Sponsor',
                'link'      =>  site_url('admin/sponsor/')
            )
        );

        $this->load->model('sponsor/Sponsor_model', 'sModel');
    }

    /**
     *  Index
     */
    public function index(){
        $this->data['title'] = "List sponsor";
        $this->data['pageTitle'] = "Sponsors";

        // include require script
        $this->data['_additionFooter'] = '
            <link rel="stylesheet" href="' . base_url() .'assets/js/zurb-responsive-tables/responsive-tables.css">
            <link rel="stylesheet" href="' . base_url() . 'assets/js/selectboxit/jquery.selectBoxIt.css">
            <script src="' . base_url() . 'assets/js/zurb-responsive-tables/responsive-tables.js"></script>
            <script src="' . base_url() . 'assets/js/selectboxit/jquery.selectBoxIt.min.js"></script>
            <script src="' . base_url() . 'assets/js/bootstrap-switch.min.js"></script>
        ';

        $this->data['sponsors'] =   $this->sModel->getAll();

        // load module view
        $this->data['_mainModule'] = $this->load->view('sponsor/list.phtml', $this->data, TRUE);

        // Notification if have flashdata message
        if($this->session->flashdata('message')){
            $this->data['_additionFooter'] .= '
                <script type="text/javascript">
                    jQuery(document).ready(function($){
                        toastr.' . $this->session->flashdata('type') . '(\'' . $this->session->flashdata('message') . '\')
                    });
                </script>
            ';
        }

        // merge module data to template
        $this->load->view('includes/_adminTemplate.phtml', $this->data);
    }

    /**
     * Function add new sponsor
     *
     * @return bool
     */
    public function add(){

        $error = array();
        $data = array(
            'sponsorname'   =>  $this->input->post('sponsorname'),
            'sponsorlink'   =>  $this->input->post('sponsorlink'),
            'sponsorlogo'   =>  $this->input->post('sponsorlogo'),
            'status'   =>  $this->input->post('status'),
            'order'   =>  $this->input->post('order'),
            'created_date'   =>  time(),
            'created_user'   =>  $this->session->userdata('userid')
        );

        if(trim($data['sponsorname']) == '' || trim($data['sponsorlink']) == '' || trim($data['sponsorlogo']) == ''){
            $error[] = "Please fill all fields";
        }

        if($data['status'] > 1){
            $error[] = "Invalid status value";
        }

        $data['order'] = intval($data['order']);

        if(count($error) === 0){
            $sponsorid = $this->sModel->insert($data);

            if($sponsorid > 0){
                $this->session->set_flashdata(array(
                    'type'      =>  'success',
                    'message'   =>  'Added sponsor <strong>' . $data['sponsorname'] . '</strong> successful.'
                ));
            }else{
                $this->session->set_flashdata(array(
                    'type'      =>  'error',
                    'message'   =>  'Database error.'
                ));
                return false;
            }
        }else{
            $this->session->set_flashdata(array(
                'type'      =>  'error',
                'message'   =>  implode('<br />', $error)
            ));
            return false;
        }
        return $sponsorid;


    }

    /**
     * Update a sponsor
     *
     * @return bool
     */
    public function update(){
        $error = array();
        $sid = $this->input->post('sponsorid');
        $data = array(
            'sponsorname'   =>  $this->input->post('sponsorname'),
            'sponsorlink'   =>  $this->input->post('sponsorlink'),
            'sponsorlogo'   =>  $this->input->post('sponsorlogo'),
            'status'   =>  $this->input->post('status'),
            'order'   =>  $this->input->post('order'),
            'created_date'   =>  time(),
            'created_user'   =>  $this->session->userdata('userid')
        );

        if(trim($data['sponsorname']) == '' || trim($data['sponsorlink']) == '' || trim($data['sponsorlogo']) == ''){
            $error[] = "Please fill all fields";
        }

        if($data['status'] > 1){
            $error[] = "Invalid status value";
        }

        $data['order'] = intval($data['order']);

        if(!$this->sModel->find('sponsorid', $sid)){
            $error[] = "Sponsor is not exists";
        }

        if(count($error) === 0){
            $affected = $this->sModel->update($data, $sid);
            $this->session->set_flashdata(array(
                'type'      =>  'success',
                'message'   =>  $affected . ' sponsor(s) updated'
            ));
            return true;
        }else{
            $this->session->set_flashdata(array(
                'type'      =>  'error',
                'message'   =>  implode('<br />', $error)
            ));
            return false;
        }
    }

    /**
     * Delete a sponsor
     *
     * @return bool
     */
    public function delete(){
        $error = array();
        $sid = $this->input->post('sponsorid');

        if(!$this->sModel->find('sponsorid', $sid)){
            $error[] = "Sponsor is not exists";
        }

        if(count($error) === 0){
            $affected = $this->sModel->delete($sid);
            $this->session->set_flashdata(array(
                'type'      =>  'success',
                'message'   =>  $affected . ' sponsor(s) deleted'
            ));
        }else{
            $this->session->set_flashdata(array(
                'type'      =>  'error',
                'message'   =>  implode('<br />', $error)
            ));
            return false;
        }
    }

}