<?php
	class FullRow extends Zend_Db_Table_Row_Abstract{
		public function refreshCleanData(){
			$this->_cleanData = $this->_data;
		}
	}