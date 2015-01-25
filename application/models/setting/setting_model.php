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
 * @version		$Id: setting_model.php  11/2/14 2:46 AM lion $
 */

class Setting_model extends CI_Model{

    /**
     *  Constructor
     */
    public function __construct(){
        parent::__construct();
    }

    /**
     * @param $modulename
     * @return mixed
     */
    public function moduleConfig($modulename){
        $result = $this->db->get_where($this->db->dbprefix('config'), array('modulename' => $modulename));

        return $result->result();
    }

    /**
     * @param $configname
     * @return mixed
     */
    public function getConfig($configname){
        $result = $this->db->get_where($this->db->dbprefix('config'), array('configname' => $configname));
        return $result->result();
    }

    /**
     * @param $config
     * @return mixed
     */
    public function setConfig($config, $configname){
        $this->db->where('configname', $configname);
        $this->db->update($this->db->dbprefix('config'), $config);

        // INSERT if not exists config
        if($this->db->affected_rows() < 1){
            $config['created_date'] =   $config['modified_date'];
            $config['created_user'] =   $config['modified_user'];
            $this->db->insert($this->db->dbprefix('config'), $config);
            return $this->db->insert_id();
        }

        return $this->db->affected_rows();
    }

}

?>
 