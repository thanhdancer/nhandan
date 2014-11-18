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
 * @version		$Id: category_model.php  10/30/14 11:38 PM lion $
 */

class Category_model extends CI_Model{

    /**
     *  Constructor
     */
    public function __construct(){
        parent::__construct();
    }

    /**
     * Find category by a field
     *
     * @param $filed
     * @param $value
     * @return mixed
     */
    public function find($filed, $value){
        $sql = "SELECT *
                FROM `" . $this->db->dbprefix('project_category') . "`
                WHERE `" . $filed . "` = ?";
        $result = $this->db->query($sql, array(
            $value
        ));

        return $result->result();
    }

    /**
     * Add new category
     *
     * @param $data
     * @return mixed
     */
    public function add($data){
        $sql = "INSERT INTO `" . $this->db->dbprefix('project_category') . "`
                (`parentid`, `categoryname`, `status`, `created_date`, `created_user`)
                VALUES
                (?, ?, ?, ?, ?)";
        $this->db->query($sql, array(
            $data['parentid'],
            $data['categoryname'],
            $data['status'],
            time(),
            $data['userid']
        ));

        return $this->db->insert_id();
    }

    /**
     * Update a category
     *
     * @param $data
     * @return mixed
     */
    public function update($data){
        $sql = "UPDATE `" . $this->db->dbprefix('project_category') . "`
                SET `parentid` = ?,
                    `categoryname` = ?,
                    `status`    = ?,
                    `modified_date` = ?,
                    `modified_user` = ?
                WHERE `categoryid` = ?
                ";

        $this->db->query($sql, array(
            $data['parentid'],
            $data['categoryname'],
            $data['status'],
            time(),
            $data['userid'],
            $data['categoryid']
        ));

        return $this->db->affected_rows();
    }

    /**
     * Delete a user group
     *
     * @param int
     * @return mixed
     */
    public function delete($categoryid = 0){

        $sql = "DELETE FROM `" . $this->db->dbprefix('project_category') . "`
                WHERE `categoryid` = ?";
        $this->db->query($sql, array(
            $categoryid
        ));

        $affected_row = $this->db->affected_rows();

        $sql = "DELETE FROM `" . $this->db->dbprefix('project') . "`
                WHERE `categoryid` = ?";

        $this->db->query($sql, array(
            $categoryid
        ));

        $affected_row += $this->db->affected_rows();

        return $affected_row;
    }


}

 ?>
 