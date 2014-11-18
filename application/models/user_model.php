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
 * @version		$Id: UserModel.php  10/22/14 11:46 PM lion $
 */

class User_model extends CI_Model{

    function __contruct(){
        parent::__construct();

    }

    /**
     * Function get all user
     * @return mixed
     */
    public function getAll(){
        $sql = "SELECT `g`.`usergroupname` AS `groupname`, `u`.*
                FROM `" . $this->db->dbprefix('user'). "` AS `u`
                LEFT JOIN `" . $this->db->dbprefix('usergroup'). "` AS `g`
                ON `u`.`usergroup` = `g`.`usergroupid`
                GROUP BY `u`.`userid`";

        $result = $this->db->query($sql);
        return $result->result();
    }

    /**
     * @param $userid
     * @return mixed
     */
    public function delete($userid){
        $sql = "DELETE FROM `" . $this->db->dbprefix('user') . "`
                WHERE `userid` = ?";
        $this->db->query($sql, array(
           $userid
        ));

        return $this->db->affected_rows();
    }

    /**
     * @param $data
     * @return mixed
     */
    public function add($data){
        $sql = "INSERT INTO `" . $this->db->dbprefix('user') . "`
                (`username`, `password`,`email`,`firstname`,`lastname`,`facebook`,`usergroup`,`avatarpath`)
                VALUES
                (?,?,?,?,?,?,?,?)
                ";
        $this->db->query($sql, array(
            $data['username'],
            $data['password'],
            $data['email'],
            $data['firstname'],
            $data['lastname'],
            $data['facebook'],
            $data['usergroup'],
            $data['avatarpath']
        ));

        return $this->db->insert_id();
    }

    /**
     * @param $data array
     */
    public function update($data){
        $where = array();
        foreach($data as $column => $value){
            if ($column == 'userid')
                continue;
            $where[] = '`' . $column . '` = ' . $this->db->escape($value);
        }
        $where = implode(',', $where);

        $sql = "UPDATE `" . $this->db->dbprefix('user') . "`
                SET " . $where . "
                WHERE `userid` = " . $this->db->escape($data['userid']);


        $this->db->query($sql);

        return $this->db->affected_rows();
    }

    /**
     * Find user by custom field
     *
     * @param $field
     * @param $value
     */
    public function find($field, $value){
        $sql = "SELECT *
                FROM `" . $this->db->dbprefix('user'). "`
                WHERE `". $field ."` = ?";

        $result = $this->db->query($sql, array(
            $value
        ));

        return $result->row();
    }

    // find info of user by username
    /**
     * @param $username
     * @return mixed
     */
    public function findUserName($username){
        $sql = "SELECT *
                FROM `" . $this->db->dbprefix('user'). "`
                WHERE `username` = ?";

        $result = $this->db->query($sql, array(
            $username
        ));

        return $result->row();
    }

    // find info of user by userid
    /**
     * @param $userid
     * @return mixed
     */
    public function findUserId($userid){
        $sql = "SELECT *
                FROM `" . $this->db->dbprefix('user'). "`
                WHERE `userid` = ?";

        $result = $this->db->query($sql, array(
            $userid
        ));

        return $result->row();
    }

    // check authentication
    /**
     * @param $username
     * @param $password
     * @return bool
     */
    public function checkLogin($username, $password){

        $sql = "SELECT `userid`, `password`
                FROM `" . $this->db->dbprefix('user'). "`
                WHERE `username` = ?";

        $result = $this->db->query($sql,  array(
           $username
        ));

        if($result->num_rows() > 0){
            $row = $result->row();

            if (md5($password) == $row->password){
                return $row->userid;
            }
            else{
                return FALSE;
            }
        }

    }
}

 ?>
 