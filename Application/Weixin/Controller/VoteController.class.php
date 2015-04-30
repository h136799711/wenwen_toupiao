<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY 杭州博也网络科技有限公司
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace Weixin\Controller;

class VoteController extends WeixinController{
	
	protected function _initliaze(){
		parent::_initialize();
		
		$this -> refreshWxaccount();
		$debug = true;
		if($debug){
			$this->getDebugUser();
		}else{
			$url = getCurrentURL();
			$this->getWxuser($url);
		}
		if(empty($this->userinfo)){
			$this->error("无法获取到用户信息！");
			exit();
		}
		$this->assign("userinfo",$this->userinfo);
	}
	
	
	public function index(){
		$this->display();	
	}
	
	/**
	 * 添加投票结果
	 */
	public function addRresult(){
		if(IS_POST){
			$wxuser_id = $this->userinfo['id'];
			$option_id = I('post.option_id',0,'intval');
			$vote_id = I('post.vote_id',0,'intval');
			if($option_id == 0 || $vote_id == 0){
				$this->error("操场失败！");
			}
			
			$entity = array(
				'wxuser_id'=>$wxuser_id,
				'option_id'=>$option_id,
				'vote_id'=>$vote_id,
			);
			//TODO: 判断用户 投了几票 ，做限制
			
			$result = apiCall("Weixin/VoteOptionResult/add",array($entity));
			
			
			if(!$result['status']){
				$this->error($result['info']);
			}
			
			$this->success($result['info']);
		}else{
			$this->error("禁止访问！");
		}
		
	}
	
	//===PRIVATE======
		
	
	//获取测试用户信息，用于PC端测试使用
	private function getDebugUser(){
		$this->userinfo = array(
			'id'=>8,
			'openid'=>'oxGH0sgeUkH4g8aowy0452xJnX1o',
			'nickname'=>'老胖子何必都',
			'avatar'=>'http://wx.qlogo.cn/mmopen/An6TFzHNImPecEhl1R3UWd26LlC1mvVgyhdh2KGCOb0yjQ4JNQnOicG2ysaKojzusSO9R3RE55Exq0lYKpVr3RRArU0u7kgjR/0',
			'score'=>0,
			'wxaccount_id'=>1,
		);
		
//		$this->wxapi = new \Common\Api\WeixinApi('wx5f9ed360f5da5370','4a0e3e50c8e9137c4873689b8ee99124');
		$this->openid = "oxGH0sgeUkH4g8aowy0452xJnX1o";
	}
	
	

	public function getWxuser($url) {

		$this -> userinfo = null;
		if (session("?userinfo")) {
			$this -> userinfo = session("userinfo");
			$this -> openid = $this->userinfo['openid'];
		}
		
		if (!is_array($this -> userinfo)) {
			
			$code = I('get.code', '');
			$state = I('get.state', '');
			if (empty($code) && empty($state)) {

				$redirect = $this -> wxapi -> getOAuth2BaseURL($url, 'HomeIndexOpenid');

				redirect($redirect);
			}

			if ($state == 'HomeIndexOpenid') {
				$accessToken = $this -> wxapi -> getOAuth2AccessToken($code);
				
				$this -> openid = $accessToken['openid'];
				$result = $this -> wxapi -> getBaseUserInfo($accessToken['openid']);

				if ($result['status']) {
					$this -> refreshWxuser($result['info']);
				} else {
					$this -> userinfo = null;
				}
			}
		}
	}

	/**
	 * 刷新粉丝信息
	 */
	private function refreshWxuser($userinfo) {
		$wxuser = array();
//		$wxuser['wxaccount_id'] = intval($this -> wxaccount['id']);
		$wxuser['nickname'] = $userinfo['nickname'];
		$wxuser['province'] = $userinfo['province'];
		$wxuser['country'] = $userinfo['country'];
		$wxuser['city'] = $userinfo['city'];
		$wxuser['sex'] = $userinfo['sex'];
		$wxuser['avatar'] = $userinfo['headimgurl'];
		$wxuser['subscribe_time'] = $userinfo['subscribe_time'];

		if (!empty($this -> openid) && is_array($this -> wxaccount)) {
			
			$map = array('openid' => $this -> openid, 'wxaccount_id' => $this -> wxaccount['id']);

			$result = apiCall('Weixin/Wxuser/save', array($map, $wxuser));

			if (!$result['status']) {
				LogRecord($result['info'], "[Home/Index/refreshWxuser]" . __LINE__);
			}else{
				$result = apiCall('Weixin/Wxuser/getInfo', array($map));
				if($result['status']){
					
					$this -> userinfo = $result['info'];
					session("userinfo", $result['info']);
				}
			}

		}

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
			$token = C('SHOP_TOKEN');
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
