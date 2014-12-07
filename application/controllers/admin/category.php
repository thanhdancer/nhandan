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
 * @version		$Id: category.php  12/6/14 11:50 AM lion $
 */
 

class Category extends CI_Controller {
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

        if (!isset($this->_user['userid'])){
            redirect('admin/user/login');
        }


        $this->data = array();
        $this->data['title'] = "Administrator Control Panel - Category | ";
        $this->data['breadcrumb'] = array(
            array(
                'name'      =>  'Home',
                'icon'      =>  'home',
                'link'      =>  site_url('admin')
            ),
            array(
                'name'      =>  'Category',
                'link'      =>  site_url('admin/category/')
            )
        );

        $this->load->model('category/Category_model', 'cModel');
    }

    /**
     * Main method
     */
    public function index($module = ''){

        if($module == ''){
            $module = array_keys($this->config->config['adminController'])[0];
        }
        $this->data['pageTitle'] = "Category";
        $this->data['module'] = $module;

        $this->data['categories'] = $this->cModel->getTreeByModule($module);

        $this->data['_additionFooter'] = '
            <link rel="stylesheet" href="' . base_url() . 'assets/js/selectboxit/jquery.selectBoxIt.css">
            <script src="' . base_url() . 'assets/js/selectboxit/jquery.selectBoxIt.min.js"></script>
        ';

        $this->data['_mainModule'] = $this->load->view('category/list.phtml', $this->data, TRUE);

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
     * Add method
     */

    public function add($module = ''){
        $this->data['pageTitle'] = "Add new category";
        $this->data['title'] .= 'Add';
        $module = ($module == '' ? array_keys($this->config->config['adminController'])[0] : $module);
        $this->data['modulename'] = $module;



        $this->data['parents'] = $this->cModel->getTreeByModule($module);
        array_push($this->data['breadcrumb'], array(
           'name'   =>  'Add new',
           'link'   =>  site_url('admin/category/add')
        ));

        // post processing
        if($this->input->post()){
            $error = array();
            $post = array();

            $post['categoryname'] = $this->input->post('categoryname');
            $post['module'] = $this->input->post('module');
            $post['parent'] = $this->input->post('parent');
            $post['status'] = $this->input->post('status');
            $post['userid'] = $this->session->userdata('userid');

            // check required fields
            if(trim($post['categoryname']) == ''){
                $error[] = "Required field must be fill.";
            }

            // check module
            if(!array_key_exists($post['module'], $this->config->config['adminController'])){
                $error[] = "Module not found.";
            }


            if(count($error) == 0){
                $this->cModel->add($post);
                $this->session->set_flashdata(array(
                    'type'      =>  'success',
                    'message'   =>  'Add new category for module <strong>' . $post['module'] . '</strong> successful.'
                ));
                redirect('admin/category/index/'.$post['module']);
            }

            $this->data['_additionFooter'] = '
                <script type="text/javascript">
                    jQuery(document).ready(function($){
                        toastr.error(\'' . implode('<br />', $error) . '\')
                    });
                </script>
            ';
        }
        $this->data['_mainModule'] = $this->load->view('category/add.phtml', $this->data, TRUE);




        $this->load->view('includes/_adminTemplate.phtml', $this->data);
    }

    public function edit($module = '', $categoryid = 0){
        $module = ($module == '' ? array_keys($this->config->config['adminController'])[0] : $module);
        $category = $this->cModel->find($categoryid)[0];
        $this->data['category'] = $category;

        $this->data['parents']   = $this->cModel->getTreeByModuleExceptId($category->module, $categoryid);
        $this->data['pageTitle'] = "Edit category &quot;" . $category->name . "&quot;";
        $this->data['title'] .=  "Edit category " . '&quot;' . $category->name . '&quot;';
        $this->data['breadcrumb'][] = array(
            'name'      =>  'Edit <strong>&quot;' . $category->name . '&quot;</strong>',
            'link'      =>  ''
        );

        $this->data['_mainModule'] = $this->load->view('category/edit.phtml', $this->data, TRUE);

        if($this->input->post()){
            $error = array();
            $post = array();
            $module = $this->input->post('module');
            $post['categoryid'] = $this->input->post('categoryid');
            $post['categoryname'] = $this->input->post('categoryname');
            $post['parent'] = $this->input->post('parent');
            $post['status'] = $this->input->post('status');
            $post['userid'] = $this->session->userdata('userid');

            // check required fields
            if(trim($post['categoryname']) == ''){
                $error[] = "Required field must be fill.";
            }


            if(count($error) == 0){
                $this->cModel->update($post);
                $this->session->set_flashdata(array(
                    'type'      =>  'success',
                    'message'   =>  'Edit category <strong>' . $post['categoryname'] . '</strong> successful.'
                ));
                redirect('admin/category/index/' . $module);
            }

            $this->data['_additionFooter'] = '
                <script type="text/javascript">
                    jQuery(document).ready(function($){
                        toastr.error(\'' . implode('<br />', $error) . '\')
                    });
                </script>
            ';

        }

        $this->load->view('includes/_adminTemplate.phtml', $this->data);

    }

    /**
     * Delete method
     * @param string $module
     * @param int    $categoryid
     */
    public function delete($module = '', $categoryid = 0){
        $module = ($module == '' ? array_keys($this->config->config['adminController'])[0] : $module);
        $affected_row = $this->cModel->delete($categoryid);
        if($affected_row > 0){
            $this->session->set_flashdata(array(
                'type'      => 'sucess',
                'message'   =>  'Deleted <strong>' . $affected_row . '</strong> category(ies) successful'
            ));
        }
        redirect('admin/category/index/' . $module);
    }
} 