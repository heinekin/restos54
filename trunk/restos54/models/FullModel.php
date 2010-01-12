<?php
abstract class FullModel extends Zend_Db_Table {
	private $_models;
	private $_loaded = FALSE;
    protected $_relations = array();
	
	function __construct($config = array()){
        parent::__construct($config);
	
        parent::setRowClass("FullRow");
    }
    
    protected function _fetch(Zend_Db_Table_Select $select){ 
        $rows = parent::_fetch($select);
		
		if($this->hasRelations() && is_array($rows) && !empty($rows)){ 
			$this->initModels();
            
			foreach($rows as &$row){ 
				foreach ($this->_models as $column => $model){ 
					if(array_key_exists($column, $row)){
                        $row[$column] = $model->find($row[$column])->current();
					} 
				}
			}
		}
		return $rows; 
	}
		
	private function hasRelations(){
		return isset($this->_relations) && !empty($this->_relations);
	}
	
	private function initModels(){
		if(!$this->_loaded){
			$this->_models = array();
			
			foreach ($this->_relations as $columnName => $modelClass){
				Zend_Loader::loadClass($modelClass); 		//Chargement du mod�le
				$this->_models[$columnName] = new $modelClass(); 	//Instanciation du mod�le
			}
							
			$this->_loaded = TRUE;
		}
	}
    
    public function createRow(array $data = array(), $defaultSource = null){

        $row = parent::createRow($data, $defaultSource);

    	if($this->hasRelations()){
    		$this->initModels();

    		foreach ($this->_models as $column => $model){ 
                $class = get_class($this);
                if(!($model instanceof $class)){
                    $row->$column = $model->createRow();
				}
    		}

    	}
    	return $row;
    }
    
    public function unload($row){
    	if($this->hasRelations()){
    		$this->_unload($row);
    		
    		$row->refreshCleanData();
    	}
    }

    public function unloadCreatedRow($row){
    	if($this->hasRelations()){
    		$this->_unload($row);
    	}
    }

    private function _unload($row){
    	$this->initModels();
    		
    	foreach ($this->_models as $column => $model){
    		if($row->$column != null && is_object($row->$column)){
    			$row->$column = $row->$column->id;
    		}
    	}
    }
    
    public function disableRelations($relations) {
        if(!empty($relations)) {
            $relations = (array)$relations;
        }
        
        $new_relations = array();
        foreach($this->_relations as $key => $value) {
            if(!in_array($key, $relations)) {
                $new_relations[$key] = $value;
            }
        }
        $this->_relations = $new_relations;
    }

    public function disableAllRelations() {
        $this->_relations = array();
    }

}