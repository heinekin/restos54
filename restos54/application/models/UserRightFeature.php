<?php
class UserRightFeature extends FullModel {
    protected $_name = 'user_right_feature';
    protected $_relations = array (
        'user_id' => 'User',
        'feature_id' => 'Feature'
    );
    
    public function fetchRightFeatures($user_id = 0)
    {
        $sql = 'SELECT
                    urf.user_id,
                    urf.feature_id,
                    urf.right,
                    f.code,
                    f.title,
                    f.module,
                    f.controller,
                    f.action,
                    f.order,
                    f.feature_type_id,
                    f.parent_id
                FROM user_right_feature AS urf
                INNER JOIN feature AS f ON urf.feature_id = f.id
                WHERE user_id = :userId';

        $bind = array(':userId' => intval($user_id, 10));

        return $this->getDefaultAdapter()->fetchAll($sql, $bind, Zend_Db::FETCH_OBJ);
    }
}