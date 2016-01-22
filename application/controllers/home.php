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
 * @version		$Id: Home.php  1/24/15 12:06 AM lion $
 */
 

if(!defined('BASEPATH')){
    exit("You cannot access this file directly");
}

class Home extends CI_Controller{

    public function __construct(){
        parent::__construct();
        $this->load->library(array('session', 'user'));
        $this->load->helper(array('language', 'url', 'form', 'file', 'text'));

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

    }

    /**
     *
     * Homepage
     *
     */
    public function index(){
        $this->load->model('project/Project_model', 'projectModel');
        $this->load->model('sponsor/Sponsor_model', 'sponsorModel');

        $data['title']      =   "Trường học nhân dân | Trang chủ";
        $data['menu']               =   "home";
        $data['map']	    =	$this->projectModel->getGroupLocation();
        $data['highlight']  =   $this->projectModel->getAll('`priority` > 1', 'priority desc');
        $data['urgent']     =   $this->projectModel->getAll('`deadline` < ' . (30 * 86400 + time()), 'priority desc');
        $data['province']   =   $this->province;
        $data['sponsors']   =   $this->sponsorModel->getAll();

        $data['_mainContent'] = $this->load->view('frontend/homepage.phtml', $data, TRUE);

        $this->load->view('includes/_homeTemplate.phtml', $data);
    }

    /**
     *  Projects
     */
    public function projects(){
        $this->load->model('project/Project_model', 'projectModel');
        $this->load->model('category/Category_model', 'categoryModel');

        $data['select_province'] = $this->uri->segment(2);
        $data['select_category'] = $this->uri->segment(3);

        $data['title']              =   "Trường học nhân dân | Các dự án";
        $data['menu']               =   "projects";

        $data['_additionHeader']    =   '<link rel="stylesheet" type="text/css" href="' . base_url() . '/assets/frontend/css/kqtk.css">';

        $where = '';
        // Make project condition
            $where .= (isset($data['select_province']) && trim($data['select_province']) != '') ?  ' AND `location` = ' . $this->db->escape($data['select_province']) : '';
        $where .= (isset($data['select_category']) && trim($data['select_category']) != '') ?  ' AND `categoryid` = ' . $this->db->escape($data['select_category']) : '';

        $data['highlight']  =   $this->projectModel->getAll('`priority` > 1' . $where, 'priority desc');
        $data['urgent']     =   $this->projectModel->getAll('`deadline` < ' . (30 * 86400 + time()) . $where, 'priority desc');
        $data['provinces']   =   $this->province;
        $data['categories'] = $this->categoryModel->getTreeByModule('project');

        $data['_mainContent'] = $this->load->view('frontend/project.phtml', $data, TRUE);
        $this->load->view('includes/_homeTemplate.phtml', $data);

    }
} 