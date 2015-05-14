<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY 杭州博也网络科技有限公司
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace Admin\Controller;

class VoteOptionController extends AdminController{
	
	protected function _initialize(){
		parent::_initialize();
		$vote_id = I('vote_id',0);
		$this->assign("vote_id",$vote_id);
	}
	
	public function index(){
		$name = I('post.name','');
		$vote_id = I('get.vote_id',0);
		$map = array();
		$map['vote_id'] = $vote_id;
		if(!empty($name)){
			$map['option_name'] = array('like','%'.$name.'%');
		}
		$page = array('curpage'=>I('get.p',0),'size'=>C('LIST_ROWS'));
		$order = " createtime desc ";
		$result = apiCall("Admin/VoteOption/query",array($map,$page,$order));
		if(!$result['status']){
			$this->error($result['info']);
		}
		
		$this->assign("name",$name);
		$this->assign("vote_id",$vote_id);
		$this->assign("list",$result['info']['list']);
		$this->assign("show",$result['info']['show']);
		$this->display();
		
	}
	
	public function add(){
		$vote_id = I('get.vote_id',0);
		if(IS_GET){
			$this->assign("enddatetime",time()+2*24*3600);
			$this->display();
		}else{
			$sort = I('post.sort');
			$option_name = I('post.option_name','');
			$img_url = I('post.img_url','');
  			
			$entity = array(
				'vote_id'=>$vote_id,
				'option_name'=>$option_name,
				'img_url'=>$img_url,
				'sort'=>$sort,
			);
			
			$result = apiCall("Admin/VoteOption/add",array($entity));
			if(!$result['status']){
				$this->error($result['info']);
			}
			$this->success("添加成功",U('Admin/VoteOption/index',array('vote_id'=>$vote_id)));
		}
	}

	
	public function delete(){
		
		$id = I('get.id',0);
		
		
		$result = apiCall("Admin/VoteOption/delete",array(array('id'=>$id)));
		
		if(!$result['status']){
			$this->error($result['info']);
		}
		
		$this->success("删除成功",U('Admin/Vote/index'));
		
	}
	
	
	public function edit(){
		
		$id = I('get.id',0);
		if(IS_GET){
		
			$result = apiCall("Admin/VoteOption/getInfo",array(array('id'=>$id)));
				
			if(!$result['status']){
				$this->error($result['info']);
			}
			$this->assign("vo",$result['info']);
			$this->display();

		}else{
			$id = I('post.id',0);
			$sort = I('post.sort');
			$option_name = I('post.option_name','');
			$img_url = I('post.img_url','');
  			$vote_id = I('post.vote_id',0);
			
			$entity = array(
				'option_name'=>$option_name,
				'img_url'=>$img_url,
				'sort'=>$sort,
			);
			
			$result = apiCall("Admin/VoteOption/saveByID",array($id,$entity));
			if(!$result['status']){
				$this->error($result['info']);
			}
			$this->success("保存成功",U('Admin/VoteOption/index',array('vote_id'=>$vote_id)));
			
		}
		
		
	}
	
	/**
	 * 结果
	 */
	public function result(){
		
		$result = apiCall("Admin/Vote/getInfo", array(array('id'=>I('vote_id',0))));
		if(!$result['status']){
			$this->error($result['info']);
		}
		
		$starttime = $result['info']['starttime'];
		$endtime = $result['info']['endtime'];
		
		$datelist = $this->getDateList($starttime,$endtime);
		
		$this->assign("datelist",$datelist);
		
		$voteOptionID = I('get.id',0);
		$result = apiCall("Admin/VoteOption/getInfo", array(array('id'=>$voteOptionID)));
		if(!$result['status']){
			$this->error($result['info']);
		}

		$this->assign("option",$result['info']);
		
		
		$curDate = strtotime(date("Y-m-d",time()));
		
		$date = I("date",$curDate,'strtotime');
		
		
		$map = array(
			'option_id'=>$voteOptionID,
		);
		$map['vote_time'] = array(array('lt',$date+24*3600),array('gt',$date));
		
		$page = array('curpage'=>I('get.p',0),'size'=>20);
		$params = array('vote_id'=>I('vote_id',0),'option_id'=>$voteOptionID,'date'=>date("Y-m-d",$date));
		
		$order = 'real_ip desc ,vote_time desc';
		$result = apiCall("Admin/VoteOptionResult/query", array($map,$page,$order,$params));
		
		if(!$result['status']){
			$this->error($result['info']);
		}
		
		$this->assign("date",date("Y-m-d",$date));
		$this->assign("id",$voteOptionID);
		$this->assign("list",$this->getRealArea($result['info']['list']));
		$this->assign("show",$result['info']['show']);
		$this->display();
		
	}
	
	
	public function getRealArea($list){
		$Ip = new \Org\Net\IpLocation('UTFWry.dat'); // 实例化类 参数表示IP地址库文件
		foreach($list as &$vo){
			$vo['_area'] = $Ip->getlocation(long2ip($vo['real_ip']));
		}
		return $list;
	}
	
	private function getDateList($starttime,$endtime){
		$date =  strtotime(date("Y-m-d",$starttime));
		
		$list = array();
		$curtime = time();
		for(;$date < $endtime && $date <= $curtime;){
			array_push($list,date("Y-m-d",$date));
			$date = $date +24*3600;
		}
		
		return $list;
		
	}
	
}

