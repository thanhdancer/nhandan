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
 * @version		$Id: article_model.php  1/25/15 2:41 PM lion $
 */
 



class Article_model extends CI_Model{

    /**
     * Constructor
     */
    public function __construct(){
        parent::__construct();
    }

    /**
     * Get all articles
     *
     * @return mixed
     */
    public function getAll(){
        $this->db->order_by('publishdate', 'desc');
        $result = $this->db->get($this->db->dbprefix('article'));
        return $result->result();
    }

    /**
     * Find an article by field and its value
     *
     * @param $field
     * @param $value
     *
     * @return mixed
     */
    public function find($field, $value){
        $result = $this->db->get_where($this->db->dbprefix('article'), array($field => $value), 1);
        return $result->result();
    }

    /**
     * Add new article
     *
     * @param $data
     *
     * @return mixed
     */
    public function insert($data){
        $this->db->insert($this->db->dbprefix('article'), $data);
        return $this->db->insert_id();
    }

    /**
     * Update an article by article ID
     *
     * @param $data
     * @param $articleid
     *
     * @return mixed
     */
    public function update($data, $articleid){
        $this->db->update($this->db->dbprefix('article'), $data, array('articleid' => $articleid));
        return $this->db->affected_rows();
    }

    /**
     * Delete an article
     * @param $articleid
     *
     * @return mixed
     */
    public function delete($articleid){
        $this->db->delete($this->db->dbprefix('article'), array('articleid' => $articleid));
        return $this->db->affected_rows();
    }



} 