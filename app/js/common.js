var HOST='http://123.56.44.104:81/';
var BRANDLOGO = 'http://123.56.44.104:81/data/brandlogo/';//品牌logo
var SERVERE='http://123.56.44.104:81/ibaoh/index.php/';//服务器
var GOODS_INDEX=SERVERE+'goods/index';//商品详情页
var CART_INFO=SERVERE+'cart/getInfo';//获取商品库存
var CART_ADD=SERVERE+'cart/add';//添加购物车
var COLLECT_INFO =  SERVERE + 'collect/index';//获取我的收藏列表
var CART_LIST = SERVERE + 'cart/index';//获取我的购物车列表
var SUREORDER_INFO = SERVERE + 'flow/index';//获取确认订单信息
var COLLECT_ADD = SERVERE + 'collect/add';//添加收藏
var COLLECT_DEL = SERVERE + 'collect/delete';//删除收藏
var ALL_ORDER = SERVERE + 'order/index';//全部订单
var GENERATE_ORDER = SERVERE + 'flow/generate';//确认订单页面，生成订单
var PAY = SERVERE + 'pay';//调取支付接口
var MY_ATTEND = SERVERE + 'focus/index'; //我的关注
var DESIGNER_LIST = SERVERE + 'brand/index';//设计师列表
var IS_LOGIN = SERVERE + 'user/login'; //用户登录
var USER_INFO = SERVERE + 'User/index'; //获取用户信息

function log(data){
	console.log(JSON.stringify(data));
}
/**
 * 获取首页属性
 * @param {Object} option
 * @param {Object} func
 */
function index(option,func){
	 Get(SERVERE,option,func);
}
/**
 * 根据参数获取商品的信息
 * @param {Object} option 参数
 * @param {Object} func  回调函数
 */
function goods_index(option,func){
	 Get(GOODS_INDEX,option,func);
}

/**
 * 判断用户是否登陆成功
 */
function is_login(option,func)
{
	Get(IS_LOGIN,option,func);
}

(function(w){
	w.Post=function(url,option,func){
		mui.ajax(url,{
			dataType:'json',//服务器返回json格式数据
			type:'post',//HTTP请求类型
			data:option,
			success:function(data){
				func(data);
			},
			error:function(xhr,type,errorThrown){
				try{ 
				if(typeof(Error)=="function"){
                       Error(xhr,type,errorThrown);
                 }
				}catch(e){
                 } 
			}
		});
	};
	w.Get=function(url,option,fun){
		mui.ajax(url,{
			dataType:'json',//服务器返回json格式数据
			type:'get',//HTTP请求类型
			data:option,
			success:function(data){
				fun(data);
			},
			error:function(xhr,type,errorThrown){
				try{ 
				if(typeof(Error)=="function"){
                       Error(xhr,type,errorThrown);
                 }
				}catch(e){
                 } 
			}
		});
	};
	/**
	 * id元素点击事件
	 * @param {Object} selector  所要操作控件的id
	 * @param {Object} func  所要执行的回调函数
	 */
	w.Click=function(selector,func){
		document.getElementById(selector).addEventListener('tap', func);
	}
	/**
	 * 批量监听点击事件
	 * @param {Object} parent 父类选择器
	 * @param {Object} selector 所要监听的所有控件选择器
	 * @param {Object} func 回调函数
	 */
	w.On=function(parent,selector,func){
		mui(parent).on('tap',selector,func);
	}
	w.LastSubstr=function(str){
		return str.substring(0,str.length-1);
	}
	
})(window);