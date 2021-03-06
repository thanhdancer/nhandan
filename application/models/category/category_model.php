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
        $this->db->select("*");
        $this->db->from($this->db->dbprefix('category'));
        $this->db->where('module', $moduleName);
        $this->db->order_by('priority', 'asc');

        $result = $this->db->get();
        return $result->result();

    }

    /**
     * @param $moduleName
     *
     * @return array
     */
    public function getTreeByModule($moduleName){
        $children = array();
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
        $children = array();
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
        $children = array();
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
                'priority'      =>  $category->priority,
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
        $result = $this->db->get_where($this->db->dbprefix('category'), array('categoryid' => $category));
        return $result->result();
    }

    //////////////////////// BACK END  /////////////////////////////////


    /**
     * Delete all rows parentid is not exists
     *
     * @return mixed
     */
    private function clearOrphan(){
        $sql = "DELETE `child` FROM `". $this->db->dbprefix('category') . "` AS `child`
                LEFT JOIN  `". $this->db->dbprefix('category') . "` AS `parent`
                  ON `child`.`parentid` = `parent`.`categoryid`
                WHERE `parent`.`categoryid` is NULL AND `child`.`parentid` != 0";
        $this->db->query($sql);
        return $this->db->affected_rows();
    }

    /**
     * Add new user group
     *
     * @param $data
     * @return mixed
     */
    public function add($data){
        $this->db->insert($this->db->dbprefix('category'), $data);
        return $this->db->insert_id();
    }

    /**
     * Update a user group
     *
     * @param $data
     * @return mixed
     */
    public function update($data, $categoryid){
        $this->db->where('categoryid', $categoryid);
        $this->db->update($this->db->dbprefix('category'), $data);
        return $this->db->affected_rows();
    }

    /**
     * Delete a user group
     *
     * @param int $userid
     * @return mixed
     */
    public function delete($categoryid){

        $this->db->where('parentid', $categoryid);
        $this->db->or_where('categoryid', $categoryid);
        $this->db->delete($this->db->dbprefix('category'));

        $affected_rows = $this->clearOrphan();
        $affected_rows += $this->db->affected_rows();

        return $affected_rows;
    }
}

 ?>
 