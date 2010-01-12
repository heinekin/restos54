<?php
class ProfileRightFeature extends FullModel {
    protected $_name = 'profile_right_feature';
    protected $_relations =  array ('profile_id' => 'Profile', 
                                    'feature_id' => 'Feature'
                                    );


   public function hasRights($feature_id, $profile_id) {
       $sql = "SELECT feature_id, profile_id, recursive
                FROM profile_right_feature
                WHERE profile_id = " . $profile_id . " AND feature_id = " . $feature_id;
       return $this->getDefaultAdapter()->fetchRow($sql);
   }

   public function getRecursiveChildren($feature_id, &$result=array()) {
       $sql = "SELECT id AS feature_id
                FROM  feature 
                WHERE parent_id = " . $feature_id;

       $rows = $this->getDefaultAdapter()->fetchAll($sql);
       foreach($rows as $item=>$value) {
           $id = $value['feature_id'];
           $result[]=$id;
           $this->getRecursiveChildren($id,$result);
       }
       return $result;
   }

   public function disableRecursiveParent($feature_id) {
       $sql = "SELECT parent_id as feature_id
                FROM feature
                WHERE id = " . $feature_id;
       $parent_row = $this->getDefaultAdapter()->fetchRow($sql);
       if ($parent_row['feature_id']!=null) {
            $this->update($parent_row, "recursive = 0");
            $this->disableRecursiveParent($parent_row['feature_id']);
       }
   }

   
   public function fetchRightFeatures($profile_id = 0)
    {
        $sql = 'SELECT
                    profile_id,
                    feature_id,
                    code,
                    title,
                    module,
                    controller,
                    action,
                    `order`,
                    feature_type_id,
                    parent_id
                FROM profile_right_feature AS prf
                INNER JOIN feature AS f ON prf.feature_id = f.id
                WHERE profile_id = :profileId';

        $bind = array(':profileId' => intval($profile_id, 10));

        return $this->getDefaultAdapter()->fetchAll($sql, $bind, Zend_Db::FETCH_OBJ);
    }

}