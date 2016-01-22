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
 * @version		$Id: permission_model.php  10/30/14 12:55 AM lion $
 */

class Usergroup_model extends CI_Model{

    public function __construct(){
        parent::__construct();
    }

    /**
     * List all group of users
     *
     * @return mixed
     */
    public function getAll(){

        $this->db->select('usergroup.*, count( ' . $this->db->dbprefix('user'). '.userid ) as numusers');
        $this->db->from($this->db->dbprefix('usergroup'));
        $this->db->join($this->db->dbprefix('user'), 'user.usergroup = usergroup.usergroupid ', 'left');
        $this->db->group_by('usergroup.usergroupid');

        /*$sql = "SELECT `g`.* , count(`u`.`userid`) as `numusers`
                FROM `" . $this->db->dbprefix('usergroup') . "` as `g`
                LEFT JOIN `" . $this->db->dbprefix('user') . "` as `u`
                ON `u`.`usergroup` = `g`.`usergroupid`
                GROUP BY `g`.`usergroupid`";*/
        $result = $this->db->get();
        return $result->result();

    }

    /**
     * Find user by field and value
     *
     * @param $field
     * @param $value
     * @return mixed
     */
    public function find($field, $value){

        $result = $this->db->get_where($this->db->dbprefix('usergroup'), array($field => $value));
        return $result->result();
    }

    /**
     * Add new user group
     *
     * @param $data
     * @return mixed
     */
    public function add($data){
        $this->db->insert($this->db->dbprefix('usergroup'), $data);
        return $this->db->insert_id();
    }

    /**
     * Update a user group
     *
     * @param $data
     * @return mixed
     */
    public function update($data, $usergroupid){
        $this->db->where('usergroupid', $usergroupid);
        $this->db->update($this->db->dbprefix('usergroup'), $data);
        return $this->db->affected_rows();
    }

    /**
     * Delete a user group
     *
     * @param int $userid
     * @return mixed
     */
    public function delete($usergroupid = 0){

        $this->db->where('usergroupid', $usergroupid);
        $this->db->delete($this->db->dbprefix('usergroup'));
        $affected_row = $this->db->affected_rows();

        $this->db->where('usergroup', $usergroupid);
        $this->db->delete($this->db->dbprefix('user'));
        $affected_row += $this->db->affected_rows();

        return $affected_row;
    }
}

 ?>
 