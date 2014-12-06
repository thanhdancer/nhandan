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
 * @version		$Id: permission.php  10/30/14 12:26 AM lion $
 */

if(!defined('BASEPATH')){
    exit('You cannot access this file directly.');
}

class Permission extends CI_Controller{

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
        $this->data['title'] = "Administrator Control Panel - Permission | ";
        $this->data['breadcrumb'] = array(
            array(
                'name'      =>  'Home',
                'icon'      =>  'home',
                'link'      =>  site_url('admin')
            ),
            array(
                'name'      =>  'Permission',
                'link'      =>  site_url('/permission/')
            )
        );

        $this->load->model('permission/Permission_model', 'pModel');

    }

    /**
     * Controller index
     */
    public function index(){

        $this->data['pageTitle'] = "List all groups";

        $this->data['groups'] = $this->pModel->getAll();

        $this->data['_additionFooter'] = '
            <link rel="stylesheet" href="' . base_url() .'assets/js/zurb-responsive-tables/responsive-tables.css">
            <link rel="stylesheet" href="' . base_url() . 'assets/js/selectboxit/jquery.selectBoxIt.css">
            <script src="' . base_url() . 'assets/js/zurb-responsive-tables/responsive-tables.js"></script>
            <script src="' . base_url() . 'assets/js/selectboxit/jquery.selectBoxIt.min.js"></script>
            <script src="' . base_url() . 'assets/js/bootstrap-switch.min.js"></script>

        ';

        $this->data['_mainModule'] = $this->load->view('permission/list.phtml', $this->data, TRUE);

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
     * Function delete a group
     *
     * @return string
     */
    public function delete(){
        $groupid = intval($this->input->post('usergroupid', TRUE));

        $affected_row = $this->pModel->delete($groupid);

        if($affected_row > 0){
            $this->session->set_flashdata(array(
                'type'      => 'success',
                'message'   => 'Deleted 1 group and <strong>' . ($affected_row - 1) . '</strong> user(s) successful.'
            ));
        }

        return json_encode('Deleted 1 group and <strong>' . ($affected_row - 1) . '</strong> user(s) successful ');
    }

    /**
     * Function add new group
     *
     * @return bool | int
     */
    public function add(){
        $data = array();
        $error = array();
        $data['usergroupname'] = $this->input->post('usergroupname', TRUE);
        $data['description'] = $this->input->post('description', TRUE);
        $data['status'] = $this->input->post('status', TRUE) == 'on' ? 1 : 0;
        $data['userid'] = $this->session->userdata('userid');

        if(trim($data['usergroupname']) == ''){
            $error[] = 'Group name cannot empty.';
        }

        if ( !in_array($data['status'], array(0,1))){
            $error[] = "Invalid status.";
        }

        if(count($error) == 0){
            $groupid = $this->pModel->add($data);
        }else{
            $this->session->set_flashdata(array(
                'type'      => 'error',
                'message'   => implode('<br />', $error)
            ));
            return false;
        }

        return $groupid;

    }

    /**
     *  Function update a user group
     *
     * @return bool
     */
    public function update(){
        $data = array();
        $error = array();
        $data['usergroupid'] = $this->input->post('usergroupid', TRUE);
        $data['usergroupname'] = $this->input->post('usergroupname', TRUE);
        $data['description'] = $this->input->post('description', TRUE);
        $data['status'] = $this->input->post('status', TRUE) == 'on' ? 1 : 0;
        $data['userid'] = $this->session->userdata('userid');

        if(trim($data['usergroupname']) == ''){
            $error[] = 'Group name cannot empty.';
        }

        if ( !in_array($data['status'], array(0,1))){
            $error[] = "Invalid status.";
        }

        if(count($error) == 0){
            $affected_row = $this->pModel->update($data);
        }else{
            $this->session->set_flashdata(array(
                'type'      => 'error',
                'message'   => implode('<br />', $error)
            ));
            return false;
        }

        return $affected_row;
    }
}
 ?>
 