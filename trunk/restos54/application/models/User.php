<?php
class User extends FullModel {
    protected $_name = 'user';
    protected $_relations =  array ('profile_id' => 'Profile');

    public function fetchWithCentre()
    {

        $sql = 'SELECT user.*, centre.nom, profile.code as profile
                FROM link_user_centre AS luc
                INNER JOIN user
                    ON user.id = luc.id_user
                INNER JOIN centre
                    ON centre.id = luc.id_centre
                INNER JOIN profile
                    ON user.profile_id = profile.id
               ';

        return Zend_Registry::get('db')->fetchAll($sql);
    }

    public function loginExist($login, $excludeId = 0) {
        if(!$this->fetchRow(array("login LIKE '".$login."'", 'id <> '.$excludeId))) {
            return false;
        } else {
            return true;
        }
    }
}