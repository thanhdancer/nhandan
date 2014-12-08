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
        $this->load->library('user');
        $this->load->helper(array('url', 'language', 'form', 'file'));

        $this->_user = $this->session->all_userdata();
    	if(!$this->user->authentication()){
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

        $this->load->model('permission/Usergroup_model', 'ugModel');
        $this->load->model('permission/Permission_model', 'permissionModel');

    }

    /**
     * Controller index
     */
    public function index(){

        $this->data['pageTitle'] = "List all groups";

        $this->data['groups'] = $this->ugModel->getAll();

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

        $affected_row = $this->ugModel->delete($groupid);

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
            $groupid = $this->ugModel->add($data);
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
            $affected_row = $this->ugModel->update($data);
        }else{
            $this->session->set_flashdata(array(
                'type'      => 'error',
                'message'   => implode('<br />', $error)
            ));
            return false;
        }

        return $affected_row;
    }
    
    ###################### Permission area #################################3
    
    /*
     * Function view permission of a group 
     * 
     * @groupid int	
     */
    public function view($groupid = 0){
    	
    	$this->data['group'] = $this->ugModel->find('usergroupid', $groupid);
    	
    	if(!$this->data['group']){
    		show_404();
    	}
    	
    	$this->data['routes'] = $this->permissionModel->getByUsergroup($groupid);
    	
    	$this->data['pageTitle'] = "View permission of group " . $this->data['group'][0]->usergroupname; 
    	
    	$this->data['_additionFooter'] = '
    	<link rel="stylesheet" href="' . base_url() .'assets/js/zurb-responsive-tables/responsive-tables.css">
    	<link rel="stylesheet" href="' . base_url() . 'assets/js/selectboxit/jquery.selectBoxIt.css">
    	<script src="' . base_url() . 'assets/js/zurb-responsive-tables/responsive-tables.js"></script>
    	<script src="' . base_url() . 'assets/js/selectboxit/jquery.selectBoxIt.min.js"></script>
    	<script src="' . base_url() . 'assets/js/bootstrap-switch.min.js"></script>
    	
    	';
    	
    	$this->data['_mainModule'] = $this->load->view('permission/permission.phtml', $this->data, TRUE);
    	
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
     * Add new route
     * 
     * @return boolean
     */
    
    public function addRoute(){
    	
    	if($this->input->post()){
    		$post	= array();
    		
    		$this->load->library('form_validation');
    		$this->form_validation->set_rules('name', "Name", 'required|xss_clean');
    		$this->form_validation->set_rules('route', "Route", 'required|xss_clean');
    		
    		if($this->form_validation->run() == FALSE){
    			$this->form_validation->set_error_delimiters('', "\n");

                echo str_replace("\n", '',validation_errors());
                return false;
    		}
    		
    		$post['name']			= $this->input->post('name');
    		$post['route']			= $this->input->post('route');
    		$post['status']			= $this->input->post('status');
    		$post['usergroupid']	= $this->input->post('group');
    		$post['created_date']	= time();
    		$post['created_user']	= $this->_user['userid'];
    		
    		// check group avaiable
    		if(!$this->ugModel->find('usergroupid', $post['usergroupid'])){
    			echo "Usergroup not avaiable";
    			return false;
    		}
    		
    		// check route
    		if(!preg_match('/^\w+\/\w+/', $post['route'])){
    			echo "Format of route must be module/method";
    			return false;
    		}
    		
    		$this->permissionModel->add($post);
    		echo "Add new route successful";
    		return false;
    		
    	}
    }
    
    public function updateRoute(){
    	if($this->input->post()){
    		$post	= array();
    		
    		$this->load->library('form_validation');
    		$this->form_validation->set_rules('name', "Name", 'required|xss_clean');
    		$this->form_validation->set_rules('route', "Route", 'required|xss_clean');
    		
    		if($this->form_validation->run() == FALSE){
    			$this->form_validation->set_error_delimiters('', "\n");
    		
    			echo str_replace("\n", '',validation_errors());
    			return false;
    		}
    		
    		$post['name']			= $this->input->post('name');
    		$post['route']			= $this->input->post('route');
    		$post['status']			= $this->input->post('status');
    		$post['usergroupid']	= $this->input->post('group');
    		$post['modified_date']	= time();
    		$post['modified_user']	= $this->_user['userid'];
    		
    		// check group avaiable
    		if(!$this->ugModel->find('usergroupid', $post['usergroupid'])){
    			echo "Usergroup not avaiable";
    			return false;
    		}
    		
    		// check route
    		if(!preg_match('/^\w+\/\w+/', $post['route'])){
    			echo "Format of route must be module/method";
    			return false;
    		}
    		
    		$this->permissionModel->update($this->input->post('permissionid'), $post);
    		echo "Add new route successful";
    		return false;
    	}
    }
    
    /**
     * Delete a route
     */
    
    public function deleteRoute(){
    	if($this->input->post()){
    		$this->permissionModel->delete($this->input->post('permissionid'));
    	}
    }
}
 ?>
 