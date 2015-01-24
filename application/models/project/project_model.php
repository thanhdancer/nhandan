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
     * @return mixed
     */
    public function getGroupLocation(){
    	$this->db->select('count(projectid) AS total, projectid, projectname, location');
    	$this->db->where('status', "2");
    	$this->db->group_by('location');
    	
    	$query = $this->db->get($this->db->dbprefix('project'));
    	
    	return $query->result();
    	
    }

    /**
     * @param string $sort
     * @param int    $page
     * @param int    $limit
     *
     * @return mixed
     */
    public function getAll($where = '1', $sort = 'created_date', $page = 0, $limit = 100){
        $sql = "SELECT COUNT(  `b`.`backerid` ) AS  `numbacker` , SUM(  `b`.`amount` ) + 0 AS  `total` ,  `p`.*
                FROM  `" . $this->db->dbprefix('project') . "` AS  `p`
                LEFT JOIN (
                SELECT  `projectid` ,  `backerid` ,  `amount`
                FROM  `" . $this->db->dbprefix('project_backers') . "`
                WHERE `status` = 1
                ) AS  `b` ON  `p`.`projectid` =  `b`.`projectid`
                WHERE $where
                GROUP BY  `p`.`projectid`
                ORDER BY $sort
                LIMIT ? , ?";

        $result = $this->db->query($sql, array(

            $page * $limit,
            $limit
        ));

        return $result->result();
    }

    /**
     * List backers 
     * 
     * @param unknown_type $projectid
     */
    
    public function listBackers($projectid = 0){
        $this->db->select('*');
        $this->db->from($this->db->dbprefix('project_backers'));
        $this->db->where(array('projectid' => $projectid));
        $result = $this->db->get();
        return $result->result();
    }

    /**
     * @param int $projectid
     *
     * @return mixed
     */
    public function find($projectid = 0){
        $sql = "SELECT COUNT(  `b`.`backerid` ) AS  `numbacker` , SUM(  `b`.`amount` ) + 0 AS  `total` ,  `p`.*
                FROM  `" . $this->db->dbprefix('project') . "` AS  `p`
                LEFT JOIN (
                SELECT  `projectid` ,  `backerid` ,  `amount`
                FROM  `" . $this->db->dbprefix('project_backers') . "`
                WHERE `status` = 1
                ) AS  `b` ON  `p`.`projectid` =  `b`.`projectid`
                WHERE `p`.`projectid` = ?
                GROUP BY  `p`.`projectid`

                ";
        $result = $this->db->query($sql, array(
            $projectid
        ));

        return $result->row();
    }

    /**
     * @param $status
     * @param $projectid
     */
    public function changeStatus($status, $projectid){
        $data = array(
            'status'        => $status,
            'modified_date' => time(),
            'modified_user' =>  $this->session->userdata['userid']
        );

        $this->db->where('projectid', $projectid);
        $this->db->update($this->db->dbprefix('project'), $data);
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
                    `address`   =   ?,
                    `status`   =   ?,
                    `priority`   =   ?,
                    `modified_date`   =   ?,
                    `modified_user`   =   ?
                WHERE `projectid`   =   ?
                ";

        $this->db->query($sql, array(
            $data['categoryid'],
            $data['projectname'],
            $data['title'],
            $data['imgthumb'],
            $data['sapo'],
            $data['content'],
            $data['deadline'],
            $data['goal'],
            $data['location'],
            $data['address'],
            $data['status'],
            $data['priority'],
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
                (`categoryid`, `projectname`, `title`, `imgthumb`, `sapo`, `content`, `deadline`, `goal`, `location`, `address`, `status`, `priority`, `created_date`, `created_user`)
                VALUES
                (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ";

        $this->db->query($sql, array(
            $data['categoryid'], // need edit
            $data['projectname'],
            $data['title'],
            $data['imgthumb'],
            $data['sapo'],
            $data['content'],
            $data['deadline'],
            $data['goal'],
            $data['location'],
            $data['address'],
            $data['status'],
            $data['priority'],
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
 