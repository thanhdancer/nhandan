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
 * @version		$Id: backer_model.php  12/8/14 12:18 PM lion $
 */
 



class Backer_model extends CI_Model{

    /**
     * Contructor
     */

    public function __contruct(){
        parent::__construct();
    }

    /**
     * @param $backerid
     */
    public function delete($backerid){
        $this->db->delete($this->db->dbprefix('project_backers'), array('backerid' => $backerid));
    }

    public function insert($data){
        $this->db->insert($this->db->dbprefix('project_backers'), $data);
    }

    public function updateStatus($backerid, $status){
        $data = array('status' => $status);
        $this->db->where('backerid', $backerid);
        $this->db->update($this->db->dbprefix('project_backers'), $data);
    }


} 