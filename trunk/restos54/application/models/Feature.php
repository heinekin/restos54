<?php
class Feature extends FullModel {
    protected $_name = 'feature';
    protected $_relations = array ('feature_type_id' => 'FeatureType', 
                                    'parent_id' => 'Feature');

    public function isLeaf($feature_id) {
        return $this->fetchRow('parent_id='.$feature_id);
    }

    public function getTree($parent_id=null, $condition=array()) {
    
        if(is_null($parent_id) || $parent_id == 0 ) {
            $where_parent = 'parent_id IS NULL';
        } else {
            //$_feature_type = $this->fetchRow('parent_id = ' . $parent_id)->toArray();
            //$feature_type = $_feature_type['feature_type_id']+1;
            $where_parent = 'parent_id = '. $parent_id;
        }
        $critere = $condition;
        $critere[] = $where_parent;

        

        $relationDisabled = false;
        if(array_key_exists('parent_id', $this->_relations)) {
            $this->disableRelations('parent_id');
            $relationDisabled = true;
        }

        
        
        $features = $this->fetchAll($critere, 'order');
        
        $tab = array();
        foreach($features as $feature) {
            
            $parent = array(
                        'id' => $feature->id, 
                        'code' => $feature->code, 
                        'title' => $feature->title, 
                        'module' => $feature->module, 
                        'controller' => $feature->controller, 
                        'action' => $feature->action, 
                        'order' => $feature->order, 
                        'feature_type_id' => $feature->feature_type_id,
                        'parent_id' =>$feature->parent_id);
            
            $children = $this->getTree($feature->id, $condition);
            if(count($children) > 0) {
                $parent['children'] = $children;
            }
            $tab[] = $parent;
        }
        
        if($relationDisabled) {
            $this->_relations[] = array('parent_id' => 'Feature');
        }
        
        return $tab;
    }
}