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
 * @version		$Id: article.php  1/25/15 2:37 PM lion $
 */
 
if (!defined("BASEPATH")){
    exit("You can't access this file directly");
}


class Article extends CI_Controller{
    private $_user;
    private $data;

    /**
     *  Constructor
     */
    public function __construct(){
        parent::__construct();
        $this->load->library(array('session', 'user'));
        $this->load->helper(array('language', 'url', 'form', 'file', 'text'));

        $this->_user = $this->session->all_userdata();

        if(!$this->user->authentication()){
            redirect('admin/user/login');
        }

        $this->data = array();
        $this->data['title'] = "Administrator Control Panel - Article | ";
        $this->data['breadcrumb'] = array(
            array(
                'name'      =>  'Home',
                'icon'      =>  'home',
                'link'      =>  site_url('admin')
            ),
            array(
                'name'      =>  'Project',
                'link'      =>  site_url('admin/article/')
            )
        );

        $this->uploadConfig  = array(
            'upload_path'   =>  'media/' . $this->_user['userid'] . '/article/',
            'allowed_types' =>  'jpg|gif|png',
            'encrypt_name'  =>  TRUE
        );

        $this->load->model('category/Category_model', 'cModel');
        $this->load->model('article/Article_model', 'aModel');
        $this->data['categories'] = $this->cModel->getTreeByModule('article');
    }

    /**
     *  List all articles
     */
    public function index(){
        $this->data['title']    .=  "List all articles";
        $this->data['pageTitle'] =  "Articles list";

        $this->data['breadcrumb'][] = array(
            'name'  =>  'List Articles',
            'link'  =>  'admin/article'
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

        $articles = $this->aModel->getAll();
        $this->data['articles'] =   array();
        foreach($articles as $post){
            $post->publishdate  =   date('d/m/Y', $post->publishdate);
            $this->data['articles'][]   =   $post;
        }

        $this->data['_mainModule'] = $this->load->view('article/listArticle.phtml', $this->data, TRUE);

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
     *  Function add new article
     */
    public function add(){
        $this->data['title']    .=  "Add new article";
        $this->data['pageTitle'] =  "Add new article";
        $this->data['breadcrumb'][] = array(
            'name'  =>  'Add article',
            'link'  =>  'admin/article/add'
        );

        $this->data['_additionFooter']  =   '
            <script src="' . base_url() . 'assets/js/jquery.inputmask.bundle.min.js"></script>
            <script src="' . base_url() . 'assets/js/tinymce/tinymce.min.js"></script>
            <script src="' . base_url() . 'assets/js/fileinput.js"></script>
        ';

        if($this->input->post()){
            $error  =   array();
            $post   =   array();

            $path = '';


            // make upload directory
            foreach(explode('/', $this->uploadConfig['upload_path']) as $tmp){
                $path .= '/' . $tmp;
                if(!file_exists(FCPATH . $path)){
                    mkdir(FCPATH . $path, 0700, true);
                }
            }

            // form validation
            $this->load->library('upload', $this->uploadConfig);
            $this->load->library('form_validation');

            $this->form_validation->set_rules('categoryid', "News category", 'required|numeric');
            $this->form_validation->set_rules('title', "Title", 'prep_for_form');
            $this->form_validation->set_rules('sapo', "Sapo", 'prep_for_form');
            $this->form_validation->set_rules('status', "Status", 'greater_than[-1]|less_than[3]');
            $this->form_validation->set_rules('priority', "Priority", 'numeric|greater_than[-1]');

            if($this->form_validation->run() == FALSE){
                $this->form_validation->set_error_delimiters('', '<br />');

                $error[] = str_replace("\n", '',validation_errors());
            }

            $post['categoryid']     =   $this->input->post('categoryid');
            $category = $this->cModel->find($post['categoryid']);
            $post['categorypath']   =   $this->slug_converter($category[0]->name);
            $post['title']          =   $this->input->post('title');
            $post['slug']           =   $this->slug_converter($post['title']);
            $post['sapo']           =   $this->input->post('sapo');
            $post['content']        =   $this->input->post('content', false);
            $post['status']         =   $this->input->post('status');
            $post['ishot']          =   $this->input->post('hot');
            $post['publishdate']    =   $this->input->post('publishdate');
            $post['numberview']     =   0;
            $post['created_user']   =   $this->_user['userid'];
            $post['created_date']   =   time();

            // check and convert publish time
            $date = explode('/', $post['publishdate']);
            $post['publishdate'] = mktime(0,0,0,$date[1],$date[0], $date[2]);

            // check uploaded file

            if(!$this->upload->do_upload('thumb')){
                $error[] = $this->upload->display_errors();
            }else{
                $uploadData = $this->upload->data();
                $post['imgthumb'] = $this->uploadConfig['upload_path'] . $uploadData['file_name'];
            }

            if(count($error) == 0){
                $this->aModel->insert($post);
                $this->session->set_flashdata(array(
                    'type'      =>  'success',
                    'message'   =>  'Article <strong>' . $post['title'] . '</strong> has been added successful.'
                ));
                redirect('admin/article/index/');
            }

            $this->data['_additionFooter'] .= '
                <script type="text/javascript">
                    jQuery(document).ready(function($){
                        toastr.error(\'' . implode('<br />', $error) . '\')
                    });
                </script>
            ';
        }

        $this->data['_mainModule'] = $this->load->view('article/addArticle.phtml', $this->data, TRUE);
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

    public function edit($articleId = 0){

        $this->data['article']  =   $this->aModel->find('articleid', $articleId);
        $this->data['article']  =   $this->data['article'][0];

        $this->data['title']    .=  "Edit article";
        $this->data['pageTitle'] =  "Edit article";
        $this->data['breadcrumb'][] = array(
            'name'  =>  '"' . $this->data['article']->title . '"',
            'link'  =>  'admin/article/edit/' . $articleId
        );

        $this->data['article']->publishdate =   date('d/m/Y', $this->data['article']->publishdate);

        $this->data['_additionFooter']  =   '
            <script src="' . base_url() . 'assets/js/jquery.inputmask.bundle.min.js"></script>
            <script src="' . base_url() . 'assets/js/tinymce/tinymce.min.js"></script>
            <script src="' . base_url() . 'assets/js/fileinput.js"></script>
        ';

        if($this->input->post()){
            $error  =   array();
            $post   =   array();

            $path = '';


            // make upload directory
            foreach(explode('/', $this->uploadConfig['upload_path']) as $tmp){
                $path .= '/' . $tmp;
                if(!file_exists(FCPATH . $path)){
                    mkdir(FCPATH . $path, 0700, true);
                }
            }

            // form validation
            $this->load->library('upload', $this->uploadConfig);
            $this->load->library('form_validation');

            $this->form_validation->set_rules('categoryid', "News category", 'required|numeric');
            $this->form_validation->set_rules('title', "Title", 'prep_for_form');
            $this->form_validation->set_rules('sapo', "Sapo", 'prep_for_form');
            $this->form_validation->set_rules('status', "Status", 'greater_than[-1]|less_than[3]');
            $this->form_validation->set_rules('priority', "Priority", 'numeric|greater_than[-1]');

            if($this->form_validation->run() == FALSE){
                $this->form_validation->set_error_delimiters('', '<br />');

                $error[] = str_replace("\n", '',validation_errors());
            }

            $aid                    =   $this->input->post('articleid');
            $post['categoryid']     =   $this->input->post('categoryid');
            $category               =   $this->cModel->find($post['categoryid']);
            $post['categorypath']   =   $this->slug_converter($category[0]->name);
            $post['title']          =   $this->input->post('title');
            $post['slug']           =   $this->slug_converter($post['title']);
            $post['sapo']           =   $this->input->post('sapo');
            $post['content']        =   $this->input->post('content', false);
            $post['status']         =   $this->input->post('status');
            $post['ishot']          =   $this->input->post('hot');
            $post['publishdate']    =   $this->input->post('publishdate');
            $post['numberview']     =   0;
            $post['modified_user']   =   $this->_user['userid'];
            $post['modified_date']   =   time();

            // check and convert publish time
            $date = explode('/', $post['publishdate']);
            $post['publishdate'] = mktime(0,0,0,$date[1],$date[0], $date[2]);

            // check uploaded file

            if($_FILES['thumb']['name']){
                if(!$this->upload->do_upload('thumb')){
                    $error[] = $this->upload->display_errors();
                }else{
                    $uploadData = $this->upload->data();
                    $post['imgthumb'] = $this->uploadConfig['upload_path'] . $uploadData['file_name'];
                }
            }else{
                $post['imgthumb'] = $this->data['article']->imgthumb;
            }

            if(count($error) == 0){
                $this->aModel->update($post, $aid);
                $this->session->set_flashdata(array(
                    'type'      =>  'success',
                    'message'   =>  'Article <strong>' . $post['title'] . '</strong> has been updated successful.'
                ));
                redirect('admin/article/index/');
            }

            $this->data['_additionFooter'] .= '
                <script type="text/javascript">
                    jQuery(document).ready(function($){
                        toastr.error(\'' . implode('<br />', $error) . '\')
                    });
                </script>
            ';
        }

        $this->data['_mainModule'] = $this->load->view('article/editArticle.phtml', $this->data, TRUE);
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
     * Delete an article
     *
     * @param int $articleId
     */
    public function delete($articleId = 0){
        $affected_row = $this->aModel->delete($articleId);
        if($affected_row > 0){
            $this->session->set_flashdata(array(
                'type'      => 'sucess',
                'message'   =>  'Deleted <strong>' . $affected_row . '</strong> article(s) successful'
            ));
        }
        redirect('admin/article/index/');
    }

    /**
     * Function generate slug from nomral sentence
     *
     * @param $str
     *
     * @return mixed
     */
    private function slug_converter ($str){
        $unicode = array(
            'a'=>'á|à|ả|ã|ạ|ă|ắ|ặ|ằ|ẳ|ẵ|â|ấ|ầ|ẩ|ẫ|ậ',
            'd'=>'đ',
            'e'=>'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ',
            'i'=>'í|ì|ỉ|ĩ|ị',
            'o'=>'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ',
            'u'=>'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự',
            'y'=>'ý|ỳ|ỷ|ỹ|ỵ',
            'A'=>'Á|À|Ả|Ã|Ạ|Ă|Ắ|Ặ|Ằ|Ẳ|Ẵ|Â|Ấ|Ầ|Ẩ|Ẫ|Ậ',
            'D'=>'Đ',
            'E'=>'É|È|Ẻ|Ẽ|Ẹ|Ê|Ế|Ề|Ể|Ễ|Ệ',
            'I'=>'Í|Ì|Ỉ|Ĩ|Ị',
            'O'=>'Ó|Ò|Ỏ|Õ|Ọ|Ô|Ố|Ồ|Ổ|Ỗ|Ộ|Ơ|Ớ|Ờ|Ở|Ỡ|Ợ',
            'U'=>'Ú|Ù|Ủ|Ũ|Ụ|Ư|Ứ|Ừ|Ử|Ữ|Ự',
            'Y'=>'Ý|Ỳ|Ỷ|Ỹ|Ỵ',
        );
        foreach($unicode as $nonUnicode=>$uni){
            $str = preg_replace("/($uni)/i", $nonUnicode, $str);
        }
        $str = preg_replace('/[^a-zA-Z0-9]/', '-', $str);
        return $str;

    }
} 