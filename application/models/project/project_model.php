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
 * @version		$Id: project_model.php  10/30/14 11:36 PM lion $
 */

class Project_model extends CI_Model{

    /**
     * Constructor
     */
    public function __construct(){
        parent::__construct();
    }

    /**
     * @param string $sort
     * @param int    $page
     * @param int    $limit
     *
     * @return mixed
     */
    public function getAll($sort = 'created_date', $page = 0, $limit = 100){
        $sql = "SELECT `p`.*, count(`b`.`backerid`) as `numbacker`, sum(`b`.`amount`) as `total`
                FROM `" . $this->db->dbprefix('project') . "` as `p`
                LEFT JOIN `" . $this->db->dbprefix('project_backers') . "` AS `b`
                ON `p`.`projectid` = `b`.`projectid`

                GROUP BY `p`.`projectid`

                ";

        $result = $this->db->query($sql, array(
            $sort,
            $page * $limit,
            $limit
        ));

        return $result->result();
    }

    /**
     * @param int $projectid
     *
     * @return mixed
     */
    public function find($projectid = 0){
        $sql = "SELECT *
                FROM `" . $this->db->dbprefix('project') . "`
                WHERE `projectid` = ?";
        $result = $this->db->query($sql, array(
            $projectid
        ));

        return $result->result();
    }

    /**
     * @param $data
     *
     * @return mixed
     */
    public function save($data){
        $sql = "UPDATE `" . $this->db->dbprefix('project') . "`
                SET `categoryid`    =   ?,
                    `projectname`   =   ?,
                    `title`   =   ?,
                    `imgthumb`   =   ?,
                    `sapo`   =   ?,
                    `content`   =   ?,
                    `deadline`   =   ?,
                    `goal`   =   ?,
                    `location`   =   ?,
                    `status`   =   ?,
                    `modified_date`   =   ?,
                    `modified_user`   =   ?
                WHERE `projectid`   =   ?
                ";

        $this->db->query($sql, array(
            0, // need edit
            $data['projectname'],
            $data['title'],
            $data['imgthumb'],
            $data['sapo'],
            $data['content'],
            $data['deadline'],
            $data['goal'],
            $data['location'],
            $data['status'],
            time(),
            $data['userid'],
            $data['projectid']
        ));

        return $this->db->affected_rows();
    }

    /**
     * Function add new project
     *
     * @param $data
     *
     * @return mixed
     */
    public function add($data){
        $sql = "INSERT INTO `" . $this->db->dbprefix('project') . "`
                (`categoryid`, `projectname`, `title`, `imgthumb`, `sapo`, `content`, `deadline`, `goal`, `location`, `status`, `created_date`, `created_user`)
                VALUES
                (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ";

        $this->db->query($sql, array(
            0, // need edit
            $data['projectname'],
            $data['title'],
            $data['imgthumb'],
            $data['sapo'],
            $data['content'],
            $data['deadline'],
            $data['goal'],
            $data['location'],
            $data['status'],
            time(),
            $data['userid']
        ));

        return $this->db->insert_id();
    }

    /**
     * @param int $projectid
     *
     * @return mixed
     */
    public function delete($projectid = 0){
        $sql = "DELETE FROM `" . $this->db->dbprefix('project_backers') . "`
                WHERE `projectid` = ?";

        $this->db->query($sql, array(
            $projectid
        ));

        $affected_row = $this->db->affected_rows();

        $sql = "DELETE FROM `" . $this->db->dbprefix('project') . "`
                WHERE `projectid` = ?";

        $this->db->query($sql, array(
            $projectid
        ));

        $affected_row += $this->db->affected_rows();

        return $affected_row;
    }

}

 ?>
 