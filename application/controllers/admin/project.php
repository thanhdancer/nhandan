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
    private $province;

    public function __construct(){
        parent::__construct();
        $this->load->library(array('session', 'user'));

        $this->load->helper(array('language', 'url', 'form', 'file', 'text'));

        $this->_user = $this->session->all_userdata();
        
        if(!$this->user->authentication()){
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
                'link'      =>  site_url('admin/project/')
            )
        );
		
        $this->province = array(
        		'VN.AG' => 'An Giang',
        		'VN.BK' => 'Bac Can',
        		'VN.BG' => 'Bac Giang',
        		'VN.BL' => 'Bac Lieu',
        		'VN.BN' => 'Bac Ninh',
        		'VN.BV' => 'BaRia-VungTau',
        		'VN.BR' => 'Ben Tre',
        		'VN.BD' => 'Binh Dinh',
        		'VN.BI' => 'Binh Duong',
        		'VN.BP' => 'Binh Phuoc',
        		'VN.BU' => 'Binh Thuan',
        		'VN.CM' => 'Ca Mau',
        		'VN.CN' => 'Can Tho',
        		'VN.CB' => 'Cao Bang',
        		'VN.DA' => 'Da Nang',
        		'VN.DC' => 'Dac Lac',
        		'VN.DO' => 'Dac Nong',
        		'VN.DB' => 'Dien Bien',
        		'VN.DN' => 'Dong Nai',
        		'VN.DT' => 'Dong Thap',
        		'VN.GL' => 'Gia Lai',
        		'VN.HG' => 'Ha Giang',
        		'VN.HM' => 'Ha Nam',
        		'VN.HT' => 'Ha Tinh',
        		'VN.HD' => 'Hai Duong',
        		'VN.HP' => 'Haiphong',
        		'VN.HN' => 'Hanoi',
        		'VN.HU' => 'Hau Giang',
        		'VN.HC' => 'Ho Chi Minh',
        		'VN.HO' => 'Hoa Binh',
        		'VN.HY' => 'Hung Yen',
        		'VN.KH' => 'Khanh Hoa',
        		'VN.KG' => 'Kien Giang',
        		'VN.KT' => 'Kon Tum',
        		'VN.LI' => 'Lai Chau',
        		'VN.LD' => 'Lam Dong',
        		'VN.LS' => 'Lang Son',
        		'VN.LO' => 'Lao Cai',
        		'VN.LA' => 'Long An',
        		'VN.ND' => 'Nam Dinh',
        		'VN.NA' => 'Nghe An',
        		'VN.NB' => 'Ninh Binh',
        		'VN.NT' => 'Ninh Thuan',
        		'VN.PT' => 'Phu Tho',
        		'VN.PY' => 'Phu Yen',
        		'VN.QB' => 'Quang Binh',
        		'VN.QM' => 'Quang Nam',
        		'VN.QG' => 'Quang Ngai',
        		'VN.QN' => 'Quang Ninh',
        		'VN.QT' => 'Quang Tri',
        		'VN.ST' => 'Soc Trang',
        		'VN.SL' => 'Son La',
        		'VN.TN' => 'Tay Ninh',
        		'VN.TB' => 'Thai Binh',
        		'VN.TY' => 'Thai Nguyen',
        		'VN.TH' => 'Thanh Hoa',
        		'VN.TT' => 'Thua Thien Hue',
        		'VN.TG' => 'Tien Giang',
        		'VN.TV' => 'Tra Vinh',
        		'VN.TQ' => 'Tuyen Quang',
        		'VN.VL' => 'Vinh Long',
        		'VN.VC' => 'Vinh Phuc',
        		'VN.YB' => 'Yen Bai'
        );
        
        $this->load->model('category/Category_model', 'cModel');
        $this->load->model('project/Project_model', 'pModel');
        $this->data['categories'] = $this->cModel->getTreeByModule('project');
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

        $this->data['projects'] = array();
        foreach($projects as $project){
            $project->deadline = date('d/m/Y', $project->deadline);
            $this->data['projects'][] = $project;
        }

        $this->data['province'] = $this->province;
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

        $this->data['province'] = $this->province;

        $this->data['_additionFooter'] = '
            <script src="' . base_url() . 'assets/js/tinymce/tinymce.min.js"></script>
	        <script src="' . base_url() . 'assets/js/jquery.inputmask.bundle.min.js"></script>
	        <script src="' . base_url() . 'assets/js/fileinput.js"></script>
        ';

        if($this->input->post()){
            $error = array();
            $post = array();

            $path = '';
            foreach(explode('/', $this->uploadConfig['upload_path']) as $tmp){
                $path .= '/' . $tmp;
                if(!file_exists(FCPATH . $path)){
                    mkdir(FCPATH . $path, 0700, true);
                }
            }

            $this->load->library('upload', $this->uploadConfig);
            $this->load->library('form_validation');

            $this->form_validation->set_rules('categoryid', "Project category", 'required|numeric');
            $this->form_validation->set_rules('projectname', "Project name", 'required|min_length[6]|prep_for_form');
            $this->form_validation->set_rules('title', "Title", 'prep_for_form');
            $this->form_validation->set_rules('sapo', "Sapo", 'prep_for_form');
            $this->form_validation->set_rules('addess', "Address", 'prep_for_form');
            $this->form_validation->set_rules('location', "Location", 'prep_for_form');
            $this->form_validation->set_rules('status', "Status", 'greater_than[-1]|less_than[3]');
            $this->form_validation->set_rules('priority', "Priority", 'required|numeric|greater_than[0]');

            if($this->form_validation->run() == FALSE){
                $this->form_validation->set_error_delimiters('', '<br />');

                $error[] = str_replace("\n", '',validation_errors());

            }

            $post['categoryid']     =   $this->input->post('categoryid');
            $post['projectname']    =   $this->input->post('projectname');
            $post['title']          =   $this->input->post('title');
            $post['sapo']           =   $this->input->post('sapo');
            $post['content']        =   $this->input->post('projectcontent', false);
            $post['deadline']       =   $this->input->post('deadline');
            $post['goal']           =   $this->input->post('goal');
            $post['location']       =   $this->input->post('location');
            $post['status']         =   $this->input->post('status');
            $post['priority']       =   $this->input->post('priority');
            $post['address']        =   $this->input->post('address');
            $post['created_user']   =   $this->_user['userid'];
            $post['created_date']   =   time();

            // check province
            if(!in_array($post['location'], array_keys($this->province) )){
            	$error[] = 'Wrong location';
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
            	$uploadData = $this->upload->data(); 
                $post['imgthumb'] = $this->uploadConfig['upload_path'] . $uploadData['file_name'];
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

        $this->data['project'] = $this->pModel->find($projectid);
        $this->data['province'] = $this->province;
        
        if(!$this->data['project']){
            show_404();
        }

        $this->data['_additionFooter'] = '
            <script src="' . base_url() . 'assets/js/tinymce/tinymce.min.js"></script>
	        <script src="' . base_url() . 'assets/js/jquery.inputmask.bundle.min.js"></script>
	        <script src="' . base_url() . 'assets/js/fileinput.js"></script>
        ';

        if($this->input->post()){
            $error = array();
            $post = array();

            $path = '';
            foreach(explode('/', $this->uploadConfig['upload_path']) as $tmp){
                $path .= '/' . $tmp;
                if(!file_exists(FCPATH . $path)){
                    mkdir(FCPATH . $path, 0700, true);
                }
            }

            $this->load->library('upload', $this->uploadConfig);
            $this->load->library('form_validation');

            $this->form_validation->set_rules('categoryid', "Project category", 'required|numeric');
            $this->form_validation->set_rules('projectname', "Project name", 'required|min_length[6]|prep_for_form');
            $this->form_validation->set_rules('title', "Title", 'prep_for_form');
            $this->form_validation->set_rules('sapo', "Sapo", 'prep_for_form');
            $this->form_validation->set_rules('address', "Address", 'prep_for_form');
            $this->form_validation->set_rules('location', "Location", 'prep_for_form');
            $this->form_validation->set_rules('status', "Status", 'greater_than[-1]|less_than[3]');
            $this->form_validation->set_rules('priority', "Priority", 'required|numeric|greater_than[0]');

            if($this->form_validation->run() == FALSE){
                $this->form_validation->set_error_delimiters('', '<br />');

                $error[] = str_replace("\n", '',validation_errors());

            }

            $post['categoryid']     =   $this->input->post('categoryid');
            $post['projectname']    =   $this->input->post('projectname');
            $post['title']          =   $this->input->post('title');
            $post['sapo']           =   $this->input->post('sapo');
            $post['content']        =   $this->input->post('projectcontent', false);
            $post['deadline']       =   $this->input->post('deadline');
            $post['goal']           =   $this->input->post('goal');
            $post['location']       =   $this->input->post('location');
            $post['address']        =   $this->input->post('address');
            $post['status']         =   $this->input->post('status');
            $post['priority']       =   $this->input->post('priority');
            $post['modified_user']  =   $this->_user['userid'];
            $post['modified_user']  =   time();
            $pid                    =   $this->input->post('projectid');

            // check province
            if(!in_array($post['location'], array_keys($this->province) )){
            	$error[] = 'Wrong location';
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
                	$uploadData = $this->upload->data();
                	$post['imgthumb'] = $this->uploadConfig['upload_path'] . $uploadData['file_name'];
                }
            }else{
                $post['imgthumb'] = $this->data['project']->imgthumb;
            }


            if(count($error) == 0){
                $this->pModel->save($post, $pid);
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

    /**
     * @param int $projectid
     */
    public function view($projectid = 0){
        $this->data['pageTitle'] = "Project review";

        $this->data['breadcrumb'][] = array(
            'name'  =>  'View project',
            'link'  =>  'admin/view/' . $projectid
        );

        $this->data['project'] = $this->pModel->find($projectid);

        if(!$this->data['project']){
            show_404();
        }

        $this->data['_mainModule'] = $this->load->view('project/view.phtml', $this->data, TRUE);
        $this->load->view('includes/_adminTemplate.phtml', $this->data);

    }


    /**
     * @param int $status
     * @param int $projectid
     */
    public function status($status = 1, $projectid = 0){

        if(!in_array($status, array(0,1,2))){
            $this->session->set_flashdata(array(
                'type'      =>  'error',
                'message'   =>  'Wrong status state.'
            ));

        }else{
            $this->pModel->changeStatus($status, $projectid);
            $this->session->set_flashdata(array(
                'type'      =>  'success',
                'message'   =>  'Status updated.'
            ));
        }


        redirect('admin/project/view/' . $projectid );
    }

    /******************* Backer **********************************/
    /**
     * @param int $projectid
     */
    public function backers($projectid = 0){

        $this->data['project'] = $this->pModel->find($projectid);
        $this->data['backers'] = $this->pModel->listBackers($projectid);

        if(!$this->data['project']){
            show_404();
        }

        $this->data['pageTitle'] = "Backers of project \"" . $this->data['project']->projectname . "\"";
        $this->data['breadcrumb'][] = array(
            'name'  =>  'Backers',
            'link'  =>  ""
        );

        $this->data['_additionFooter'] = '
            <link rel="stylesheet" href="' . base_url() .'assets/js/zurb-responsive-tables/responsive-tables.css">
            <link rel="stylesheet" href="' . base_url() . 'assets/js/selectboxit/jquery.selectBoxIt.css">
            <script src="' . base_url() . 'assets/js/zurb-responsive-tables/responsive-tables.js"></script>
            <script src="' . base_url() . 'assets/js/selectboxit/jquery.selectBoxIt.min.js"></script>
            <script src="' . base_url() . 'assets/js/bootstrap-switch.min.js"></script>

        ';

        $this->data['_mainModule'] = $this->load->view('project/backerlist.phtml', $this->data, TRUE);

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
     *  Delete a backer
     */
    public function deletebacker(){
        if($this->input->post()){
            $this->load->model('project/Backer_model', 'bModel');
            $this->bModel->delete($this->input->post('backerid'));
        }
    }

    /**
     * Update backer status
     */
    public function updateBackerStatus(){
        if($this->input->post()){
            $this->load->model('project/Backer_model', 'bModel');
            $this->bModel->updateStatus($this->input->post('backerid'), $this->input->post('status'));
        }
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
 