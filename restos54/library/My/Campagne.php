<?php

class My_Campagne {

    private $_selected;
    private $_year;
    private $_desc;
    private $_type;
    private $_semaine;

    public function __construct($selected = 0) {
        $this->setSelected($selected);
    }

    public function setSelected($camp) {
        $this->_selected = $camp;
    }

    public function getSelected() {
        return $this->_selected;
    }

    public function setSemaine($sem) {
        $this->_semaine = $sem;
    }

    public function getSemaine() {
        return $this->_semaine;
    }
    
    public function get_year() {
        return $this->_year;
    }

    public function set_year($year) {
        $this->_year = $year;
    }

    public function get_desc() {
        return $this->_desc;
    }

    public function set_desc($desc) {
        $this->_desc = $desc;
    }
    
    public function get_type() {
        return $this->_type;
    }

    public function set_type($type) {
        $this->_type = $type;
    }



}
