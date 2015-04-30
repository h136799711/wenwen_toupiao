<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="zh">

	<head>
		<meta charset="utf-8">
		<title><?php echo C('WEBSITE_TITLE');?></title>
		<meta name="keywords" content="<?php echo ((isset($seo["keywords"]) && ($seo["keywords"] !== ""))?($seo["keywords"]):" "); ?>" />
		<meta name="description" content="<?php echo ((isset($seo["description"]) && ($seo["description"] !== ""))?($seo["description"]):" "); ?>" />
		<meta name="author" content="<?php echo ((isset($cfg["owner"]) && ($cfg["owner"] !== ""))?($cfg["owner"]):"杭州博也网络科技"); ?>" />
	    <meta content="yes" name="apple-mobile-web-app-capable" />
	    <meta content="yes" name="apple-touch-fullscreen" />
	    <meta content="telephone=no,email=no" name="format-detection" />
	    <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no" />
		
		<!-- 系统基本样式 -->
		<link type="text/css" rel="stylesheet" href="/github/wenwen_toupiao/Public/cdn/amazeui/2.3.0/css/amazeui.min.css" />
		
		<link type="text/css" rel="stylesheet" href="/github/wenwen_toupiao/Public/Weixin/css/vote.css?v=1430399678" />
		
		<!-- 脚本 -->
		<script type="text/javascript" src="/github/wenwen_toupiao/Public/cdn/jquery/2.1.1/jquery.min.js"></script>
		<script type="text/javascript" src="/github/wenwen_toupiao/Public/cdn/amazeui/2.3.0/js/amazeui.min.js"></script>
		<script type="text/javascript" src="/github/wenwen_toupiao/Public/Weixin/js/common.js?v=1430399678"></script>
				
		
	<!--顶部区域 -->
	<style type="text/css">
		.vote-page{
			font-size:0.8rem;
			padding: 10px;
		}
		.vote-page .vote{
			padding: 10px;
		}
		.vote-page .vote-item {
			padding: 10px;
			border-bottom: 1px solid #E7CAD5;
		}
		.vote-page .vote-item .vote-img{
			width:75px;
			height: 75px;
		}
		.vote-page .vote{
			background: #FFFFFF;
			border: 1px solid #E7CAD5;
			border-radius:5px;
		}
	</style>


	</head>
	
	<body class="theme-default theme-vote">

		
	<div class="vote-page"> 
			<div class="vote">
				<div class="group-name">最佳新人</div>
				<div class="vote-item">
					<img  src="/github/wenwen_toupiao/Public/Weixin/imgs/banner12.jpg"  class="vote-img"/>
				</div>
				<div class="vote-item">
					<img  src="/github/wenwen_toupiao/Public/Weixin/imgs/banner12.jpg"  class="vote-img"/>
				</div>
				<div class="vote-item">
					<img  src="/github/wenwen_toupiao/Public/Weixin/imgs/banner12.jpg"  class="vote-img"/>
				</div>
			</div>
	</div>
	<div class="vote-page"> 
		<?php if(is_array($groups)): $i = 0; $__LIST__ = $groups;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><div class="group-name"><?php echo ($vo["name"]); ?></div>
			<?php if(is_array($vo["votes"])): $i = 0; $__LIST__ = $vo["votes"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vote): $mod = ($i % 2 );++$i;?><div class="vote-item">
					
				</div><?php endforeach; endif; else: echo "" ;endif; endforeach; endif; else: echo "" ;endif; ?>
	</div>

		



		<footer data-am-widget="footer" class="am-footer am-footer-default" data-am-footer="{  }">
			<div class="am-footer-switch">
			</div>
			<div class="am-footer-miscs ">
				<p>由
					<a href="http://www.itboye.com" title="杭州博也网络科技" target="_blank" class="">杭州博也网络科技</a>提供技术支持</p>
				<p>&copy;2013-<?php echo date('Y');?> Version <?php echo C('APP_VERSION');?> <?php echo C('WEBSITE_OWNER');?></p>
				<p><?php echo C('WEBSITE_ICP');?></p>
				<p>{__RUNTIME__}</p>
			</div>
		</footer>
		<div data-am-widget="gotop" class="am-gotop am-gotop-fixed">
		  <a href="#top" title="回到顶部">
		    <span class="am-gotop-title">顶部</span>
		    <i class="am-gotop-icon am-icon-chevron-up"></i>
		  </a>
		</div>
		
		<script type="text/html" src="<?php echo U('Tool/Task/index');?>"></script>
	</body>

</html>