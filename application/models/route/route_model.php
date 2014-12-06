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

class Route_model extends CI_Model{

    public function __construct(){
        parent::__construct();
    }

    /**
     * List all group of users
     *
     * @return mixed
     */
    public function getAll(){

        $sql = "SELECT *
                FROM `" . $this->db->dbprefix('modules') . "`
                ";
        $result = $this->db->query($sql);
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
        $sql = "SELECT *
                FROM `" . $this->db->dbprefix('usergroup') . "`
                WHERE " . $field . "` = ?";
        $result = $this->db->query($sql, array(
           $value
        ));

        return $result->result();
    }

    /**
     * Add new user group
     *
     * @param $data
     * @return mixed
     */
    public function add($data){
        $sql = "INSERT INTO `" . $this->db->dbprefix('usergroup') . "`
                (`usergroupname`, `description`, `status`, `created_date`, `created_user`)
                VALUES
                (?, ?, ?, ?, ?)
                ";

        $this->db->query($sql, array(
            $data['usergroupname'],
            $data['description'],
            $data['status'],
            time(),
            $data['userid']
        ));

        return $this->db->insert_id();
    }

    /**
     * Update a user group
     *
     * @param $data
     * @return mixed
     */
    public function update($data){
        $sql = "UPDATE `" . $this->db->dbprefix('usergroup') . "`
                SET `usergroupname` = ?,
                    `description` = ?,
                    `status`    = ?,
                    `modified_date` = ?,
                    `modified_user` = ?
                WHERE `usergroupid` = ?
                ";

        $this->db->query($sql, array(
            $data['usergroupname'],
            $data['description'],
            $data['status'],
            time(),
            $data['userid'],
            $data['usergroupid']
        ));

        return $this->db->affected_rows();
    }

    /**
     * Delete a user group
     *
     * @param int $userid
     * @return mixed
     */
    public function delete($usergroupid = 0){

        $sql = "DELETE FROM `" . $this->db->dbprefix('usergroup') . "`
                WHERE `usergroupid` = ?";
        $this->db->query($sql, array(
           $usergroupid
        ));

        $affected_row = $this->db->affected_rows();

        $sql = "DELETE FROM `" . $this->db->dbprefix('user') . "`
                WHERE `usergroup` = ?";

        $this->db->query($sql, array(
            $usergroupid
        ));

        $affected_row += $this->db->affected_rows();

        return $affected_row;
    }
}

 ?>
 