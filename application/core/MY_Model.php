<?php
/*
	Author: Sovit Tamrakar
	URL: http://ssovit.com

*/


if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class MY_Model extends CI_Model {

    public $tbl = "";
    public $primary_key = "";
    public $primary_name = "";
    public function __construct() {
        parent::__construct();
   }

    function get($type = "all", $order_field = '', $order = 'ASC', $limit = false, $offset = '',$joins=array(),$select=false) {
		if(!$select){
				$this->db->select($this->tbl.'.*');
		}else{
				$this->db->select($select);
		}
		if ($this->db->field_exists($this->tbl.'.'.'created', $this->tbl)) {
			$this->db->select('YEAR('.$this->tbl.'.created) as year, MONTH('.$this->tbl.'.created) as month, DAY('.$this->tbl.'.created) as day ');
		}
        if ($limit!==false) {
            $this->db->limit($limit, $offset);
        }
 		if (is_array($type) && !empty($type)) {
			foreach ($type as $keys => $vals) {
				if($vals!==false){
					if(!is_array($vals)){
						$this->db->where($keys, $vals);
					}else{
						foreach($vals as $key1=>$val1){
							if(method_exists($this->db,$keys)){
								if(is_array($val1)){
									foreach($val1 as $key2=>$val2){
										 call_user_func_array(array($this->db,$keys),array($key2,$val2));
									}
									}else{
								 call_user_func_array(array($this->db,$keys),array($key1,$val1));
										}
							}
						}
						}
				}else{
				$this->db->where($keys);
					
					}
			}
		}
		foreach($joins as $join){
			$this->db->join($join['table'],$join['joinOn'],$join['dir']);
			$this->db->select($join['select']);
			if(isset($join['groupBy'])){
				$this->db->group_by($join['groupBy']);
			}
			}
        if ($order_field != '') {
            if ($order == '') {
                $this->db->order_by($order_field);
            } else {
                $this->db->order_by($order_field, $order);
            }
        }
        $query = $this->db->get($this->tbl);
      return $query->result();
    }

	function getTotal($type='all',$joins=array())
	{
		$this->db->select($this->tbl.'.'.$this->primary_key);
 		if (is_array($type) && !empty($type)) {
			foreach ($type as $keys => $vals) {
				if($vals!==false){
					if(!is_array($vals)){
						$this->db->where($keys, $vals);
					}else{
						foreach($vals as $key1=>$val1){
							if(method_exists($this->db,$keys)){
								if(is_array($val1)){
								 call_user_func_array(array($this->db,$keys),array($key1,$val1[0],$val1[1]));
									}else{
								 call_user_func_array(array($this->db,$keys),array($key1,$val1));
										}
							}
						}
						}
				}else{
				$this->db->where($keys);
					
					}
			}
		}
		foreach($joins as $join){
			$this->db->join($join['table'],$join['joinOn'],$join['dir']);
			$this->db->select($join['select']);
			if(isset($join['groupBy'])){
				$this->db->group_by($join['groupBy']);
			}
			}
		$query = $this->db->get($this->tbl);
		return $query->num_rows();
	}
	function insert($data) {
        if ($this->db->field_exists('created', $this->tbl)) {
            $data['created'] = date('c');
        }
        if ($this->db->field_exists('modified', $this->tbl)) {
            $data['modified'] = date('c');
        }
		$this->db->insert($this->tbl, $data);
		return $this->db->insert_id();
	}

    function update($data, $id=false) {
        if ($this->db->field_exists('modified', $this->tbl)) {
            $data['modified'] = date('c');
        }
		if(!is_array($id)){
        	$this->db->where($this->primary_key, $id);
		}
		else{
			foreach($id as $where=>$val){
				$this->db->where($where,$val);
			}
		}
        $this->db->update($this->tbl, $data);
        return $id;
    }

	function delete($id,$attrib=array())
	{	
	if($id!==false){
		$this->db->where($this->primary_key, $id);
	}
		if(count($attrib)>0){
		foreach($attrib as $akey=>$aval){
		$this->db->where($akey, $aval);
			}
		}
		$this->db->delete($this->tbl);
	}
	function deleteByName($name,$attrib=array())
	{
		$this->db->where($this->primary_name, $name);
		if(count($attrib)>0){
		foreach($attrib as $akey=>$aval){
		$this->db->where($akey, $aval);
			}
		}
		$this->db->delete($this->tbl);
	}
	function fromId($id,$attrib=array(),$joins=array())
	{
        $this->db->select($this->tbl.'.*');
		if ($this->db->field_exists(''.$this->tbl.'.created', $this->tbl)) {
			$this->db->select('YEAR('.$this->tbl.'.created) as year, MONTH('.$this->tbl.'.created) as month, DAY('.$this->tbl.'.created) as day ');
		}
		$this->db->where($this->tbl.'.'.$this->primary_key, $id);
		if(!empty($attrib)){
		foreach($attrib as $akey=>$aval){
		$this->db->where($akey, $aval);
			}
		}
		foreach($joins as $join){
			$this->db->join($join['table'],$join['joinOn'],$join['dir']);
			$this->db->select($join['select']);
			if(isset($join['groupBy'])){
				$this->db->group_by($join['groupBy']);
			}
			}
		$query = $this->db->get($this->tbl);
		$row   = $query->row();
		if (is_object($row)) {
			return $row;
		}
		return false;
	}
	function fromName($name, $attrib=array(),$joins=array())
	{
        $this->db->select($this->tbl.'.*');
		if ($this->db->field_exists('created', $this->tbl)) {
			$this->db->select('YEAR(created) as year, MONTH(created) as month, DAY(created) as day ');
		}
		$this->db->where($this->primary_name, $name);
		if(count($attrib)>0){
		foreach($attrib as $akey=>$aval){
		$this->db->where($akey, $aval);
			}
		}
		foreach($joins as $join){
			$this->db->join($join['table'],$join['joinOn'],$join['dir']);
			$this->db->select($join['select']);
			if(isset($join['groupBy'])){
				$this->db->group_by($join['groupBy']);
			}
			}
		$query = $this->db->get($this->tbl);
		$row   = $query->row();
		if (is_object($row)) {
			return $row;
		}
		return false;
	}
    function ifExist($id) {
        if (is_object($this->fromId($id))) {
            return true;
        }
        return false;
    }
	function truncate(){
		$this->db->query('TRUNCATE TABLE '.$this->tbl);
		}
	function fieldData(){
		return $this->db->field_data($this->tbl);
	}
}
