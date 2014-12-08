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
 * @author		vv0lll - vv0lll.nogroup@gmail.com
 * @since			1.0
 * @version		$Id: permission_model.php  Dec 8, 2014 6:22:23 PMZ lion $
 */
class Permission_model extends CI_Model{
	
	public function __construct(){
		parent::__construct();
	}
	
	/**
	 * Function get all record
	 */
	
	public function getAll(){
		$query = $this->db->get($this->db->dbprefix('permission'));
		return $query->result();
	}
	
	/**
	 * Get permission by usergroup
	 * 
	 * @param int $groupid
	 */
	
	public function getByUsergroup($groupid = 0, $checkstatus = false){
		if ($checkstatus){
			$query = $this->db->get_where($this->db->dbprefix('permission'), array(
					'usergroupid' 	=> 	$groupid,
					'status'		=>	1
			));
		}else{
			$query = $this->db->get_where($this->db->dbprefix('permission'), array(
					'usergroupid' 	=> 	$groupid
			));
		}
		
		
		return $query->result();
	}
	
	############################# Back end ####################################
	/**
	 * 
	 * Function insert new permission
	 * 
	 * @param unknown_type $data
	 */
	
	public function add($data){
		$this->db->insert($this->db->dbprefix('permission'), $data);
		return $this->db->insert_id();
	}
	
	/**
	 * Function update a permission
	 * 
	 * @param unknown_type $id
	 * @param unknown_type $data
	 */
	
	public function update($id, $data){
		$this->db->where('permissionid', $id);
		$this->db->update($this->db->dbprefix('permission'), $data);
		return $this->db->affected_rows();
	}
	
	/**
	 * Delete a permission row
	 * 
	 * @param unknown_type $id
	 */
	
	public function delete($id){
		$this->db->delete($this->db->dbprefix('permission'), array('permissionid' => $id));
		return $this->db->affected_rows();
	}
	
} 
 ?>
 
 