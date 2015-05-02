<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY 杭州博也网络科技有限公司
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace Weixin\Controller;

class VoteController extends WeixinController{
	
	protected function _initialize(){
		parent::_initialize();
		C('SHOW_PAGE_TRACE',false);
		$this -> refreshWxaccount();
//		$debug = false;
//		if($debug){
//			$this->getDebugUser();
//		}else{
//			$url = getCurrentURL();
//			$this->getWxuser($url);
//		}
//		if(empty($this->userinfo)){
//			$this->error("无法获取到用户信息！");
//			exit();
//		}
		$this->getCurrentUser();
		$this->assign("userinfo",$this->userinfo);
	}
	
	
	public function result(){
		//
		$group = I('group','');
		
		$map = array();
		$map['group'] = $group;
		
		$order = "sort asc";
		$result = apiCall("Weixin/Vote/queryNoPaging",array($map,$order));
		if(!$result['status']){
			$this->error($result['info']);
		}
		
		$result_arr = array();
//		$this->assign("groups",);
		
		
		$tmpArr = array();
		$sortArr = array();
		foreach($result['info']  as $vo){
			$entity = array(
				'vote_name'=>$vo['vote_name'],
				'sort'=>$vo['sort'],
				'endtime'=>$vo['endtime'],
				'is_end'=>0,//是否结束
				'_total'=>0,//总参与人数
				'_options'=>array(),
			);
			if($vo['endtime'] - time() <= 0){
				$entity['is_end'] = 1;
			}
			$sortArr[$vo['sort']] = $vo['id'];
			$result_arr[$vo['id']] = $entity;
			array_push($tmpArr,$vo['id']);						
		}
		if(count($tmpArr) == 0){
			array_push($tmpArr,-1);
		}
		
		unset($map['group']);
		$map['vote_id'] = array('in',$tmpArr);
		$order =  " sort desc ";
		$result = apiCall("Weixin/VoteOption/queryNoPaging", array($map,$order));
		
		if(!$result['status']){
			$this->error($result['info']);
		}
		$option_ids = array();
		foreach($result['info'] as $vo){
			$entity = array(
				'option_id'=>$vo['id'],
				'option_name'=>$vo['option_name'],
				'sort'=>$vo['sort'],
				'img_url'=>$vo['img_url'],
				'vote_id'=>$vo['vote_id'],
				'_vote_cnt'=>0 , // 投票统计
				'_rank'=>-1,
			);
			array_push($option_ids,$vo['id']);
			$result_arr[$vo['vote_id']]['_options'][$vo['id']] = $entity;
		}
		
		if(count($option_ids) == 0){
			array_push($option_ids,-1);
		}
		
		//获取选项统计信息
		$result = apiCall("Weixin/VoteOptionResult/voteCount", array($option_ids));
		
		if(!$result['status']){
			$this->error($result['info']);
		}
		
		foreach($result['info'] as $key=>$vo){
			$cnt = intval($vo['cnt']);
			$result_arr[$vo['vote_id']]['_total'] = $result_arr[$vo['vote_id']]['_total'] + $cnt;
			$result_arr[$vo['vote_id']]['_options'][$vo['option_id']]['_vote_cnt'] = $cnt;
			if(!isset($result_arr[$vo['vote_id']]['_options']['_flag_rank'])){
				$result_arr[$vo['vote_id']]['_options']['_flag_rank'] = 0;
			}
			$result_arr[$vo['vote_id']]['_options']['_flag_rank'] = $result_arr[$vo['vote_id']]['_options']['_flag_rank'] + 1;
			$result_arr[$vo['vote_id']]['_options'][$vo['option_id']]['_rank'] = ($result_arr[$vo['vote_id']]['_options']['_flag_rank']);
		}
		
		ksort($sortArr,SORT_NUMERIC);
		
//		dump($result_arr);
		$result_arr = $this->getTop($result_arr,$i);
//		dump($result_arr);
		$this->assign("resultArr",$result_arr);
		$this->display();
		
	}

	
	public function index(){
		//
		$group = I('group','');
		
		$map = array();
		$map['group'] = $group;
		
		$order = "sort asc";
		$result = apiCall("Weixin/Vote/queryNoPaging",array($map,$order));
		if(!$result['status']){
			$this->error($result['info']);
		}
		
		$result_arr = array();
		
		
		$tmpArr = array();
		$sortArr = array();
		foreach($result['info']  as $vo){
			$entity = array(
				'vote_name'=>$vo['vote_name'],
				'sort'=>$vo['sort'],
				'endtime'=>$vo['endtime'],
				'_total'=>0,//总参与人数
				'_options'=>array(),
			);
			if($vo['endtime'] - time() <= 0){
				$entity['is_end'] = 1;
			}
			$sortArr[$vo['sort']] = $vo['id'];
			$result_arr[$vo['id']] = $entity;
			array_push($tmpArr,$vo['id']);						
		}
		if(count($tmpArr) == 0){
			array_push($tmpArr,-1);
		}
		
		unset($map['group']);
		$map['vote_id'] = array('in',$tmpArr);
		$order =  " sort desc ";
		$result = apiCall("Weixin/VoteOption/queryNoPaging", array($map,$order));
		
		if(!$result['status']){
			$this->error($result['info']);
		}
		$option_ids = array();
		foreach($result['info'] as $vo){
			$entity = array(
				'option_id'=>$vo['id'],
				'option_name'=>$vo['option_name'],
				'sort'=>$vo['sort'],
				'img_url'=>$vo['img_url'],
				'vote_id'=>$vo['vote_id'],
				'_vote_cnt'=>0 , // 投票统计
				'_rank'=>0,
			);
			array_push($option_ids,$vo['id']);
			$result_arr[$vo['vote_id']]['_options'][$vo['id']] = $entity;
		}
		
		if(count($option_ids) == 0){
			array_push($option_ids,-1);
		}
		
		//获取选项统计信息
		$result = apiCall("Weixin/VoteOptionResult/voteCount", array($option_ids));
		
		if(!$result['status']){
			$this->error($result['info']);
		}
		
		foreach($result['info'] as $key=>$vo){
			$cnt = intval($vo['cnt']);
			$result_arr[$vo['vote_id']]['_total'] = $result_arr[$vo['vote_id']]['_total'] + $cnt;
			$result_arr[$vo['vote_id']]['_options'][$vo['option_id']]['_vote_cnt'] = $cnt;
			$result_arr[$vo['vote_id']]['_options'][$vo['option_id']]['_rank'] = $key;
		}
		
		ksort($sortArr,SORT_NUMERIC);
		
		foreach($result_arr as &$vo){
			
			//TODO: 对其进行排序
			
			$list = &$vo['_options'];
			
			
		}
		
		$this->assign("sortArr",$sortArr);
		$this->assign("resultArr",$result_arr);
		$this->display();	
	}
	
	private function getTop($result_arr,$lastRank) {
		$result = array();
		
		foreach($result_arr as $vo){
			$entity = array(
				'vote_name'=>$vo['vote_name'],
				'sort'=>$vo['sort'],
				'is_end'=>$vo['is_end'],//是否结束
				'_total'=>$vo['_total'],//总参与人数
				'_top_options'=>array(),
			);
			$maxRank = $vo['_options']['_flag_rank']+1;
			$extra = $vo['_options']['_flag_rank']+1;
			$top = count($vo['_options']);
			foreach($vo['_options'] as $key=>$option){
				if($key == '_flag_rank'){
					continue;
				}
				if($option['_rank'] == -1){
					$option['_rank'] = $maxRank;					
					$maxRank = $maxRank + 1;
					
					
					if($extra <= $top){
						$entity['_top_options'][$extra] = $option;
						$extra = $extra + 1;
					}
					
				}elseif($option['_rank'] <= $top){
					$entity['_top_options'][$option['_rank']] = $option;
				}
			}
			ksort($entity['_top_options'],SORT_NUMERIC);
			array_push($result,$entity);
		}
		
		return $result;
		
	}
	
	
	
	
	
	
	


	

	
	/**
	 * 添加投票结果
	 */
	public function addResult(){
		if(IS_POST){
			
			$perUserMaxTicket = 2;//每天5票同一ip
			
			$wxuser_id = $this->userinfo['id'];
			$real_ip = $this->userinfo['real_ip'];
			$option_id = I('get.option_id',0,'intval');
			$vote_id = I('get.vote_id',0,'intval');
			
			if($option_id == 0 || $vote_id == 0){
				$this->error("操场失败！");
			}
			
			$entity = array(
				'wxuser_id'=>$wxuser_id,
				'real_ip'=> $real_ip,
				'option_id'=>$option_id,
				'vote_id'=>$vote_id,
			);
			
			$map = array();
			$map['option_id'] = $option_id;
//			$map['vote_id'] = $vote_id;
			$map['real_ip'] = $real_ip;
			$today = date("Y-m-d",time());
			
			$today = strtotime($today);
			$map['vote_time'] = array(array('lt',time()),array('gt',$today));
			
			//TODO: 判断用户 投了几票 ，做限制			
			$result = apiCall("Weixin/VoteOptionResult/count", array($map));
			
			if(!$result['status']){
				$this->error($result['info']);
			}
			$cnt = intval($result['info']);
			
			if($perUserMaxTicket - $cnt <= 0){
				$this->error("感谢您的支持，请明天再来投！");
			}
			
			$result = apiCall("Weixin/VoteOptionResult/add",array($entity));
			
			
			if(!$result['status']){
				$this->error($result['info']);
			}
			
			$this->success("感谢您的一票！");
		}else{
			$this->error("禁止访问！");
		}
		
	}
	
	//===PRIVATE======


	private function getCurrentUser(){
		$this->userinfo = array(
			'real_ip'=>ip2long(get_client_ip()),
			'id'=>0,
		);
	}
	
	/**
	 * 刷新
	 */
	private function refreshWxaccount() {
		$token = I('get.token', '');
		if (!empty($token)) {
			session("shop_token", $token);
		} elseif (session("?shop_token")) {
			$token = session("shop_token");
		}
		if(empty($token)){
			$token = C('WEIXIN_TOKEN');
		}
		$result = apiCall('Weixin/Wxaccount/getInfo', array( array('token' => $token)));
		if ($result['status'] && is_array($result['info'])) {
			$this -> wxaccount = $result['info'];
			$this -> wxapi = new \Common\Api\WeixinApi($this -> wxaccount['appid'], $this -> wxaccount['appsecret']);
		} else {
			exit("公众号信息获取失败，请重试！");
		}
	}
	
}
