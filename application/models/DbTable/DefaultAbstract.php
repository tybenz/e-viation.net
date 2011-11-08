<?php 

class Application_Model_DbTable_DefaultAbstract extends Zend_Db_Table_Abstract 
{ 
    
    
    public function add($data) 
    {
        return $this->insert($data);
    }
     
    public function edit($data) 
    { 
		$primary = $this->getPrimary();
		$db = $this->getAdapter();
		if(isset($data[$primary]) && ($data[$primary] != NULL) && ($data[$primary] != '')) {
			$id = $data[$primary];
			unset($data[$primary]);
			$this->update($data, $primary . ' = '. (int)$id);
			return $id;
		} else {
			$this->insert($data);
			return $db->lastSequenceId($this->_sequence);
		}
    }
     
    public function del($id) 
    { 
		$primary = $this->getPrimary();
        $this->delete($primary . ' = ' . (int)$id); 
    }

    public function get($id) 
    { 
        $db = $this->getAdapter();
 		$primary = $this->getPrimary();
 		if(is_numeric($id)){
 		    $id = $db->quote($id, 'INTEGER'); 
	    } else {
	        $id = $db->quote($id);
        }
        
        $row = $this->fetchRow($primary . ' = ' . $id); 
        if (!$row) { 
            throw new Exception("Could not find row $id"); 
        } 
        return $row->toArray();    
    }
     
    public function getAll(){
        $rows = $this->fetchAll(); 
        if (!$rows) { 
            throw new Exception("No rows found"); 
        } 
        return $rows->toArray();
    }
    
	public function getPrimary()
	{
		$primary = $this->_primary;
		if( is_string($primary) ) {
			return $primary;
		} else if( is_array($primary) && count($primary) == 1 ) {
			return $primary[1];
		} else {
			throw new Exception("Primary key contains multiple fields");
		}
	}

	public function getTableName()
	{
		return $this->_name;
	}
} 
