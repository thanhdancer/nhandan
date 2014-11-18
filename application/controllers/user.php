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
 * @version		$Id: User.php  10/22/14 3:15 PM lion $
 */

if(!defined('BASEPATH')){
    exit("You cannot access this file directly");
}

class User extends CI_Controller{

    private $_user;
    private $userModel;
    private $_template = array();

    /**
     *  Constructor
     */
    public function __construct() {
        parent::__construct();
        $this->load->library('session');
        $this->load->helper(array('url','language', 'form', 'file'));

        $this->_user = $this->session->all_userdata();
        if (!isset($this->_user['userid']) &&  $this->router->fetch_method() != 'login' ){
           redirect('/user/login');
        }

        $this->load->Model('User_model');

        $this->userModel = $this->User_model;
    }

    /**
     *  Index
     */
    public function index(){
        redirect('/user/display');
    }

    /**
     *  Function login
     */
    public function login(){

        // process post data

        if($this->input->post('username')){

            // initalize variable
            $data = array(
                'username' => $this->input->post('username'),
                'password' => $this->input->post('password')
            );

            // check valid data
            if (!$data['username']){
                show_error("You must enter username");
            }

            if( !$data['password']){
                show_error("You mus enter password");
            }

            // check password
            $this->load->Model('User_model');
            $loginStatus = $this->User_model->checkLogin($data['username'], $data['password']);
            if ($loginStatus != FALSE){

                $userinfo = $this->User_model->findUserId($loginStatus);
                $this->session->set_userdata(array(
                    'userid' => $userinfo->userid,
                    'username' => $userinfo->username,
                    'group' => $userinfo->usergroup,
                    'firstname' => $userinfo->firstname,
                    'lastname' => $userinfo->lastname,
                    'avatar' => $userinfo->avatarpath
                ));
                echo json_encode(array('login_status' => 'success'));
                return TRUE;
            }
            else{
                echo json_encode(array('login_status' => 'invalid'));
                return FALSE;
            }
        }

        // check logged in
        if( !$this->session->userdata('userid') ){
            $data['title'] = "Login";
            $data['_header'] = $this->load->view("includes/_header.phtml", $data, TRUE);
            $data['_footer'] = $this->load->view("includes/_footer.phtml", '', TRUE);
            $this->load->view("includes/_loginTemplate.phtml", $data);
        }
        else{
            redirect('/admin/');
        }
    }

    /**
     *  Function logout
     */
    public function logout(){
        $this->session->unset_userdata(array(
            'userid' => '',
            'username' => '',
            'group' => '',
            'firstname' => '',
            'lastname' => '',
            'avatar' => ''
        ));
        redirect('/user/login');
    }

    /**
     *  Function edit user
     * @param int $userid
     */
    public function edit($userid = 0){

        $user = $this->userModel->findUserId($userid);

        if($this->input->post()){
            $post = array();
            $post['password'] = $this->input->post('password', TRUE);
            $post['usergroup'] = $this->input->post('usergroup', TRUE);
            $post['firstname'] = $this->input->post('firstname', TRUE);
            $post['lastname'] = $this->input->post('lastname', TRUE);
            $post['avatarpath'] = $this->input->post('avatar', TRUE);
            $post['email'] = $this->input->post('email', TRUE);
            $post['facebook'] = $this->input->post('facebook', TRUE);
            $post['userid'] = $user->userid;

            if ($post['password'] != ''){
                $post['password'] = md5($post['password']);
            }
            else{
                unset($post['password']);
            }

            $status = $this->userModel->update($post);

            if($status != 0){
                $update = array(
                    'firstname' => $post['firstname'],
                    'lastname' => $post['lastname'],
                    'avatar' => $post['avatarpath']
                );
                $this->session->set_userdata($update);

                $this->session->set_flashdata(array(
                    'type'      => 'success',
                    'message'   => 'Update ' . $post['username'] . ' user successful'
                ));
            }
            redirect('/user/display');

        }

        // get data

        $data = array();
        $data['title'] = "Administrator Control Panel - User | Edit " . $user->username;
        $data['breadcrumb'] = array(array(
            'name' => 'Home',
            'icon' => 'home',
            'link' => site_url('admin')
        ), array(
            'name' => 'User',
            'link' => site_url('/user/')
        ), array(
            'name' => 'Edit',
            'link' => site_url('/user/edit/' . $user->userid)
        ));

        $data['pageTitle'] = "Edit user";
        $data['USER'] = $user;

        $this->load->Model('permission/Permission_model', 'pModel');
        $data['groups'] = $this->pModel->getAll();

        $data['_mainModule'] = $this->load->view('user/edit.phtml', $data, TRUE);
        $this->load->view('includes/_adminTemplate.phtml', $data);

    }

    /**
     * Display all users
     */
    public function display(){

        $data = array();
        $data['title'] = "Administrator Control Panel - User | List all user";
        $data['breadcrumb'] = array(array(
            'name' => 'Home',
            'icon' => 'home',
            'link' => site_url('admin')
        ), array(
            'name' => 'User',
            'link' => site_url('/user/')
        ));
        $data['pageTitle'] = "List all user";
        $data['_additionFooter'] = '
            <link rel="stylesheet" href="' . base_url() .'assets/js/zurb-responsive-tables/responsive-tables.css">
            <link rel="stylesheet" href="' . base_url() . 'assets/js/selectboxit/jquery.selectBoxIt.css">
            <script src="' . base_url() . 'assets/js/zurb-responsive-tables/responsive-tables.js"></script>
            <script src="' . base_url() . 'assets/js/selectboxit/jquery.selectBoxIt.min.js"></script>
        ';
        $data['users'] = $this->userModel->getAll();
        $data['_mainModule'] = $this->load->view('user/list.phtml', $data, TRUE);


        if($this->session->flashdata('message')){
            $data['_additionFooter'] .= '
                <script type="text/javascript">
                    jQuery(document).ready(function($){
                        toastr.' . $this->session->flashdata('type') . '(\'' . $this->session->flashdata('message') . '\')
                    });
                </script>
            ';
        }


        $this->load->view('includes/_adminTemplate.phtml', $data);
    }

    /**
     * Add new user
     */
    public function add(){

        $data = array();
        $data['title'] = "Administrator Control Panel - User | Add " ;
        $data['breadcrumb'] = array(array(
            'name' => 'Home',
            'icon' => 'home',
            'link' => site_url('admin')
        ), array(
            'name' => 'User',
            'link' => site_url('/user/')
        ), array(
            'name' => 'Add',
            'link' => site_url('/user/add/')
        ));

        $data['pageTitle'] = "Add new user";

        $this->load->Model('permission/Permission_model', 'pModel');
        $data['groups'] = $this->pModel->getAll();

        $data['_mainModule'] = $this->load->view('user/add.phtml', $data, TRUE);


        if($this->input->post()){
            $error = array();
            $post = array();
            $post['username'] = $this->input->post('username', TRUE);
            $post['password'] = $this->input->post('password', TRUE);
            $post['usergroup'] = $this->input->post('usergroup', TRUE);
            $post['firstname'] = $this->input->post('firstname', TRUE);
            $post['lastname'] = $this->input->post('lastname', TRUE);
            $post['avatarpath'] = $this->input->post('avatar', TRUE);
            $post['email'] = $this->input->post('email', TRUE);
            $post['facebook'] = $this->input->post('facebook', TRUE);

            // check required field
            if (trim($post['password']) == '' || trim($post['username']) == '' || trim($post['email']) == '' ){
                $error[] = "Required field must be fill.";
            }

            // check usergroup
//            if ( !in_array($post['usergroup'], array(1,2,3))){
//                $error[] = "Wrong usergroup.";
//            }


            // check exists username
            if( count($this->userModel->findUserName($post['username'])) > 0){
                $error[] = $post['username'] . " has exists.";
            }


            // check exists email
            if( count($this->userModel->find('email', $post['email']))){
                $error[] = $post['email'] . " has exists.";
            }

            $post['password'] = md5($post['password']);


            if(count($error) == 0){
                $this->userModel->add($post);
                $this->session->set_flashdata(array(
                    'type'      => 'success',
                    'message'   => 'Add user <strong>'. $post['username'] .'</strong> successful'
                ));
                redirect('/user/display');
            }
            $data['_additionFooter'] = '
                <script type="text/javascript">
                    jQuery(document).ready(function($){
                        toastr.error(\'' . implode('<br />', $error) . '\')
                    });
                </script>
            ';
        }
        $this->load->view('includes/_adminTemplate.phtml', $data);

    }

    /**
     * Delete user
     *
     * @param int $userid
     */
    public function delete($userid = 0){
        $affected_rows = $this->userModel->delete($userid);

        if($affected_rows > 0){
            $this->session->set_flashdata(array(
                'type'      => 'success',
                'message'   => 'Deleted <strong>'. $affected_rows .'</strong> user(s) successful'
            ));
        }
        redirect('/user/display/');
    }
}

 ?>
 