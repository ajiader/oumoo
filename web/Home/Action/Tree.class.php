<?php 
class tree{
	public $row;
	public $tree;
	
	public function init($row){
		$this->row = $row;
		return $this;
	}
	
	public function order($id='-1',$topid='-1',$a='&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',$b='└─',$index='',$top_id=false){
		foreach ($this->row as $row){
			if($top_id && $row['id'] != $top_id){
				continue;//如果指定了顶级栏目 则只显示该栏目以及下级栏目
			}
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