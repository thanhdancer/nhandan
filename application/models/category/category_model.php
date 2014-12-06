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

class Category_model extends CI_Model{

    public function __construct(){
        parent::__construct();
    }


    /**
     * @param $moduleName
     *
     * @return mixed
     */
    public function getByModule($moduleName){
        $sql = "SELECT *
                FROM `" . $this->db->dbprefix('category') . "`
                WHERE `module` = ?";

        $result = $this->db->query($sql, array(
            $moduleName
        ));

        return $result->result();

    }

    /**
     * @param $moduleName
     *
     * @return array
     */
    public function getTreeByModule($moduleName){
        $children = [];
        $categories = self::getByModule($moduleName);

        if($categories){
            foreach($categories as $category){

                $parentId = $category->parentid;

                if($parentId === null){
                    continue;
                }

                $parentId = (string) $parentId;
                if( !isset($children[$parentId])){
                    $children[$parentId] = array();
                }

                $children[$parentId][] =$category;
            }
        }

        $data = array(
            'identifier'    =>  'category_id',
            'label'         =>  'name',
            'items'         =>  self::_getDataTree('0', $children)
        );

        return $data;

    }


    /**
     * @param $moduleName
     * @param $categoryid
     *
     * @return array
     */
    public function getTreeByModuleExceptId($moduleName, $categoryid){
        $children = [];
        $categories = self::getByModule($moduleName);

        if($categories){
            foreach($categories as $category){

                $parentId = $category->parentid;

                if($parentId === null){
                    continue;
                }

                $parentId = (string) $parentId;
                if( !isset($children[$parentId])){
                    $children[$parentId] = array();
                }

                $children[$parentId][] =$category;
            }
        }

        unset($children[$categoryid]);
        $data = array(
            'identifier'    =>  'category_id',
            'label'         =>  'name',
            'items'         =>  self::_getDataTree('0', $children)
        );

        return $data;

    }

    /**
     * @param $categoryid
     *
     * @return array
     */
    public function getTreeById($categoryid){
        $children = [];
        $details = self::find($categoryid);
        $categories = self::getByModule($details[0]->module);

        if($categories){
            foreach($categories as $category){

                $parentId = $category->parentid;

                if($parentId === null){
                    continue;
                }

                $parentId = (string) $parentId;
                if( !isset($children[$parentId])){
                    $children[$parentId] = array();
                }

                $children[$parentId][] =$category;
            }
        }

        $data = array(
            'identifier'    =>  'category_id',
            'label'         =>  'name',
            'items'         =>  self::_getDataTree($categoryid, $children)
        );

        return $data;
    }

    /**
     * @param       $parentid
     * @param       $children
     * @param int   $depth
     * @param array $items
     *
     * @return array
     */
    private static function _getDataTree($parentid, $children, $depth = -1, $items = array()){
        $depth++;

        $parentid = (string) $parentid;

        if(!isset($children[$parentid])){
            return array();
        }

        foreach($children[$parentid] as $category){
            $item = array(
                'categoryid'    =>  $category->categoryid,
                'name'          =>  $category->name,
                'module'        =>  $category->module,
                'parentid'      =>  $category->parentid,
                'status'        =>  $category->status,
                'numberOfItems' =>  isset($children[$category->categoryid]) ? count($children[$category->categoryid]) : 0,
                'depth'         =>  $depth
            );
            $items[] = $item;
            if($item['numberOfItems'] > 0){
                $items = self::_getDataTree($category->categoryid, $children, $depth, $items);
            }
        }
        return $items;
    }

    /**
     * @param int $category
     *
     * @return mixed
     */

    public function find($category = 0){
        $sql = "SELECT *
                FROM `" . $this->db->dbprefix('category') . "`
                WHERE `categoryid` = ?";
        $result = $this->db->query($sql, array(
           $category
        ));

        return $result->result();
    }

    //////////////////////// BACK END  /////////////////////////////////
    /**
     * Add new user group
     *
     * @param $data
     * @return mixed
     */
    public function add($data){
        $sql = "INSERT INTO `" . $this->db->dbprefix('category') . "`
                (`name`, `module`, `parentid`, `status`, `created_user`, `created_date`)
                VALUES
                (?, ?, ?, ?, ?, ?)
                ";

        $this->db->query($sql, array(
            $data['categoryname'],
            $data['module'],
            $data['parent'],
            $data['status'],
            $data['userid'],
            time()

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
        $sql = "UPDATE `" . $this->db->dbprefix('category') . "`
                SET `name` = ?,
                    `parentid` = ?,
                    `status`    = ?,
                    `modified_date` = ?,
                    `modified_user` = ?
                WHERE `categoryid` = ?
                ";

        $this->db->query($sql, array(
            $data['categoryname'],
            $data['parent'],
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
     * @param int $userid
     * @return mixed
     */
    public function delete($categoryid){



        $sql = "DELETE FROM `" . $this->db->dbprefix('category') . "`
                WHERE `parentid` = ?";

        $this->db->query($sql, array(
            $categoryid
        ));

        $affected_row = $this->db->affected_rows();

        $sql = "DELETE FROM `" . $this->db->dbprefix('category') . "`
                WHERE `categoryid` = ?";

        $this->db->query($sql, array(
            $categoryid
        ));

        $affected_row += $this->db->affected_rows();

        return $affected_row;
    }
}

 ?>
 