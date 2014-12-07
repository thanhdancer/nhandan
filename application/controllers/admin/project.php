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
    private $uploadConfig;

    public function __construct(){
        parent::__construct();
        $this->load->library('session');
        $this->load->helper(array('language', 'url', 'form', 'file', 'text'));

        $this->_user = $this->session->all_userdata();
        if(!isset($this->_user['userid']) && substr($this->router->fetch_method(),0,5) == 'admin' ){
            redirect('admin/user/login');
        }

        $this->uploadConfig  = array(
            'upload_path'   =>  'media/' . $this->_user['userid'] . '/project/',
            'allowed_types' =>  'jpg|gif|png',
            'encrypt_name'  =>  TRUE
        );

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

        $this->load->model('category/Category_model', 'cModel');
        $this->load->model('project/Project_model', 'pModel');
    }

    /**
     *  Method list all projects
     */
    public function index(){
        $this->data['pageTitle'] = "List all projects";
        $this->data['breadcrumb'][] = array(
            'name'  =>  'List projects',
            'link'  =>  'admin/project'
        );

        $this->data['_additionFooter'] = '
            <link rel="stylesheet" href="' . base_url() . 'assets/js/datatables/responsive/css/datatables.responsive.css">
            <link rel="stylesheet" href="' . base_url() . 'assets/js/select2/select2-bootstrap.css">
            <link rel="stylesheet" href="' . base_url() . 'assets/js/select2/select2.css">
            <script src="' . base_url() . 'assets/js/jquery.dataTables.min.js"></script>
            <script src="' . base_url() . 'assets/js/dataTables.bootstrap.js"></script>
            <script src="' . base_url() . 'assets/js/datatables/jquery.dataTables.columnFilter.js"></script>
            <script src="' . base_url() . 'assets/js/datatables/lodash.min.js"></script>
            <script src="' . base_url() . 'assets/js/datatables/responsive/js/datatables.responsive.js"></script>
            <link rel="stylesheet" href="' . base_url() . 'assets/js/selectboxit/jquery.selectBoxIt.css">
            <script src="' . base_url() . 'assets/js/selectboxit/jquery.selectBoxIt.min.js"></script>
        ';

        $projects = $this->pModel->getAll();

        $this->data['projects'] = [];
        foreach($projects as $project){
            $project->deadline = date('d/m/Y', $project->deadline);
            $this->data['projects'][] = $project;
        }


        $this->data['_mainModule'] = $this->load->view('project/list.phtml', $this->data, TRUE);

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
     * Add new project
     */
    public function newproject(){
        $this->data['pageTitle'] = "Add new project";
        $this->data['breadcrumb'][] = array(
            'name'  =>  "Add new",
            'link'  =>  "admin/project/newproject"
        );

        $this->data['_additionFooter'] = '
            <script src="' . base_url() . 'assets/js/tinymce/tinymce.min.js"></script>
	        <script src="' . base_url() . 'assets/js/jquery.inputmask.bundle.min.js"></script>
	        <script src="' . base_url() . 'assets/js/fileinput.js"></script>
        ';

        if($this->input->post()){
            $error = [];
            $post = [];

            $path = '';
            foreach(explode('/', $this->uploadConfig['upload_path']) as $tmp){
                $path .= '/' . $tmp;
                if(!file_exists(FCPATH . $path)){
                    mkdir(FCPATH . $path, 0700, true);
                }
            }

            $this->load->library('upload', $this->uploadConfig);
            $this->load->library('form_validation');

            $this->form_validation->set_rules('projectname', "Project name", 'required|min_length[6]|htmlspecialchars');
            $this->form_validation->set_rules('title', "Title", 'htmlspecialchars');
            $this->form_validation->set_rules('sapo', "Sapo", 'htmlspecialchars');
            $this->form_validation->set_rules('location', "Location", 'htmlspecialchars');
            $this->form_validation->set_rules('status', "Status", 'greater_than[-1]|less_than[3]');

            if($this->form_validation->run() == FALSE){
                $this->form_validation->set_error_delimiters('', '<br />');

                $error[] = str_replace("\n", '',validation_errors());

            }

            $post['projectname'] = $this->input->post('projectname');
            $post['title'] = $this->input->post('title');
            $post['sapo'] = $this->input->post('sapo');
            $post['content'] = $this->input->post('projectcontent', false);
            $post['deadline'] = $this->input->post('deadline');
            $post['goal'] = $this->input->post('goal');
            $post['location'] = $this->input->post('location');
            $post['status'] = $this->input->post('status');
            $post['userid'] = $this->_user['userid'];


            // check required field
            if(trim($post['projectname']) == '' || trim($post['deadline']) == '' || trim($post['goal']) == '' || trim($post['location']) == ''){
                $error[] = "Required field(s) need fill.";
            }

            // check and convert deadline time
            $date = explode('/', $post['deadline']);
            $post['deadline'] = mktime(0,0,0,$date[1],$date[0], $date[2]);

            // check and convert goal (currency)
            preg_match_all('!\d+!', $post['goal'], $match);
            $post['goal'] = implode('', $match[0]) * 1;

            // check uploaded file

            if(!$this->upload->do_upload('thumb')){
                $error[] = $this->upload->display_errors();
            }else{
                $post['imgthumb'] = $this->uploadConfig['upload_path'] . $this->upload->data()['file_name'];
            }

            if(count($error) == 0){
                $this->pModel->add($post);
                $this->session->set_flashdata(array(
                    'type'      =>  'success',
                    'message'   =>  'Project <strong>' . $post['projectname'] . '</strong> has been added successful.'
                ));
                redirect('admin/project/index/');
            }

            $this->data['_additionFooter'] .= '
                <script type="text/javascript">
                    jQuery(document).ready(function($){
                        toastr.error(\'' . implode('<br />', $error) . '\')
                    });
                </script>
            ';

        }

        $this->data['_mainModule'] = $this->load->view('project/add.phtml', $this->data, TRUE);
        $this->load->view('includes/_adminTemplate.phtml', $this->data);
    }

    /**
     * Edit a project
     *
     * @param int $projectid
     */
    public function edit($projectid = 0){
        $this->data['pageTitle'] = "Edit project";
        $this->data['breadcrumb'][] = array(
            'name'  =>  "Edit",
            'link'  =>  ""
        );

        $this->data['project'] = $this->pModel->find($projectid)[0];


        $this->data['_additionFooter'] = '
            <script src="' . base_url() . 'assets/js/tinymce/tinymce.min.js"></script>
	        <script src="' . base_url() . 'assets/js/jquery.inputmask.bundle.min.js"></script>
	        <script src="' . base_url() . 'assets/js/fileinput.js"></script>
        ';

        if($this->input->post()){
            $error = [];
            $post = [];

            $path = '';
            foreach(explode('/', $this->uploadConfig['upload_path']) as $tmp){
                $path .= '/' . $tmp;
                if(!file_exists(FCPATH . $path)){
                    mkdir(FCPATH . $path, 0700, true);
                }
            }

            $this->load->library('upload', $this->uploadConfig);
            $this->load->library('form_validation');

            $this->form_validation->set_rules('projectname', "Project name", 'required|min_length[6]|htmlspecialchars');
            $this->form_validation->set_rules('title', "Title", 'htmlspecialchars');
            $this->form_validation->set_rules('sapo', "Sapo", 'htmlspecialchars');
            $this->form_validation->set_rules('location', "Location", 'htmlspecialchars');
            $this->form_validation->set_rules('status', "Status", 'greater_than[-1]|less_than[3]');

            if($this->form_validation->run() == FALSE){
                $this->form_validation->set_error_delimiters('', '<br />');

                $error[] = str_replace("\n", '',validation_errors());

            }


            $post['projectname'] = $this->input->post('projectname');
            $post['title'] = $this->input->post('title');
            $post['sapo'] = $this->input->post('sapo');
            $post['content'] = $this->input->post('projectcontent', false);
            $post['deadline'] = $this->input->post('deadline');
            $post['goal'] = $this->input->post('goal');
            $post['location'] = $this->input->post('location');
            $post['status'] = $this->input->post('status');
            $post['userid'] = $this->_user['userid'];
            $post['projectid'] = $this->input->post('projectid');

            // check required field
            if(trim($post['projectname']) == '' || trim($post['deadline']) == '' || trim($post['goal']) == '' || trim($post['location']) == ''){
                $error[] = "Required field(s) need fill.";
            }

            // check and convert deadline time
            $date = explode('/', $post['deadline']);
            $post['deadline'] = mktime(0,0,0,$date[1],$date[0], $date[2]);

            // check and convert goal (currency)
            preg_match_all('!\d+!', $post['goal'], $match);
            $post['goal'] = implode('', $match[0]) * 1;

            // check uploaded file

            if($_FILES['thumb']['name']){
                if(!$this->upload->do_upload('thumb')){
                    $error[] = $this->upload->display_errors();
                }else{
                    $post['imgthumb'] = $this->uploadConfig['upload_path'] . $this->upload->data()['file_name'];
                }
            }else{
                $post['imgthumb'] = $this->data['project']->imgthumb;
            }


            if(count($error) == 0){
                $this->pModel->save($post);
                $this->session->set_flashdata(array(
                    'type'      =>  'success',
                    'message'   =>  'Project <strong>' . $post['projectname'] . '</strong> has been edited successful.'
                ));
                redirect('admin/project/index/');
            }

            $this->data['_additionFooter'] .= '
                <script type="text/javascript">
                    jQuery(document).ready(function($){
                        toastr.error(\'' . implode('<br />', $error) . '\')
                    });
                </script>
            ';

        }

        $this->data['_mainModule'] = $this->load->view('project/edit.phtml', $this->data, TRUE);
        $this->load->view('includes/_adminTemplate.phtml', $this->data);
    }

    /**
     * Delete method
     * @param int    $projectid
     */
    public function delete($projectid = 0){
        $affected_row = $this->pModel->delete($projectid);
        if($affected_row > 0){
            $this->session->set_flashdata(array(
                'type'      => 'sucess',
                'message'   =>  'Deleted <strong>' . $affected_row . '</strong> category(ies) successful'
            ));
        }
        redirect('admin/project/index/');
    }

    public function config(){

        $data = array();
        print_r($this->config);
        die();
        $data['pageTitle'] = "Config module <strong>Project</strong>";


        $this->data['_mainModule'] = $this->load->view('project/config.phtml', $data, TRUE);
        $this->load->view('includes/_adminTemplate.phtml', $this->data);
    }
}
 ?>
 