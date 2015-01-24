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
 * @version		$Id: sponsor_model.php  1/24/15 12:07 PM lion $
 */
 



class Sponsor_model extends CI_Model{

    /**
     *  Contructor
     */
    public function __construct(){
        parent::__construct();
    }

    /**
     * Get all active sponsor
     *
     * @return mixed
     */
    public function getAll(){
        $this->db->select('*');
        $this->db->from($this->db->dbprefix('sponsor'));
        $this->db->order_by('order', 'asc');
        $result = $this->db->get();

        return $result->result();
    }


    public function find($field, $value){
        $this->db->select('*');
        $this->db->from($this->db->dbprefix('sponsor'));
        $this->db->where($field, $value);
        $this->db->order_by('order', 'asc');
        $result = $this->db->get();

        return $result->result();
    }

    ############################################################
    /**
     * Insert new sponsor
     *
     * @param $data
     */
    public function insert($data){
        $this->db->insert($this->db->dbprefix('sponsor'), $data);
        return $this->db->insert_id();
    }

    /**
     * Update exists sponsor
     *
     * @param $data
     * @param $sid
     */
    public function update($data, $sid){
        $this->db->where('sponsorid', $sid);
        $this->db->update('sponsor', $data);
        return $this->db->affected_rows();
    }

    /**
     * Delete sponsor by id
     *
     * @param $sid
     */
    public function delete($sid){
        $this->db->where('sponsorid', $sid);
        $this->db->delete('sponsor');
        return $this->db->affected_rows();
    }



} 