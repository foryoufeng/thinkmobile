## thinkmobile
*  thinkmobile是数据库和后台基于EcShop，Api基于ThinkPHP，前端基于mui的移动App应用（不要问我为什么都是基于，因为你懂得）

## 程序说明
*  server文件夹是基于ThinkPHP开发的api程序
*  app文件夹是基于mui开发的app程序

## 程序安装

*  在本地安装好EcShop,下载好文件将thinkmobile/server并放入网站可访问的地方，如http://localhost/thinkmobile/
*  配置thinkmobile/Application/Common/Conf/config.php，进行数据库的连接
*  注意，thinkmobile和EcShop并没有什么关系,只是用了它的数据库而已,其他后台程序并没有使用它的，所以可以正常使用EcShop（需自行修改EcShop的自带bug,比如会删除session过期中购物车中的商品）

##App

*  App使用的是mui开发的，由于是前端妹子开发的,程序问题大家就不要见怪了，你们看着改吧，不过大部分功能还是没什么问题的
*  如何运行？使用的是hbulider,不知道怎么用请参考[官方文档](http://www.dcloud.io/)

##后记
*  看到开头可能你就不想看了，因为谁还用EcShop那么古老的东西，可是没办法，偏偏会有人用，而且还很多，所以我把这个开源出来供大家学习，可以用它的数据库和参考它的程序
*  但是绝不在它上面写一行代码，api文档请看api.doc中定义的

## 商业友好的开源协议

thinkmobile遵循Apache2开源协议发布。Apache Licence是著名的非盈利开源组织Apache采用的协议。该协议和BSD类似，鼓励代码共享和尊重原作者的著作权，同样允许代码修改，再作为开源或商业软件发布。
