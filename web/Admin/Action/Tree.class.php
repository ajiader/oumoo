<?php 
class tree{
	public $row;
	public $tree;
	
	public function init($row){
		$this->row = $row;
		return $this;
	}
	
	public function order($id='-1',$topid='-1',$a='&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',$b='└─',$index=''){
		foreach ($this->row as $row){
			if($row['class_top'] == $id){
				if($id != $topid){
					$row['class_name'] = $index.$b.$row['class_name'];
				};
				$this->tree[] = $row;
				$this->order($row['id'],$topid,$a,$b,$a.$index);
			}
		}
		return true;
	}

}