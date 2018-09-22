<h1 align="center">thinkphp-china 论坛</h1>

<p align="center">:rainbow: 基于Thinkphp5.0和fly社区前端模板构建PHP论坛.</p>

[![Build Status](https://s1.ax1x.com/2018/09/09/iihuUP.png)](https://www.thinkphp.cn)
[![Build Status](https://s1.ax1x.com/2018/09/09/iihZDA.png)](https://fly.layui.com)
![downloads 32M/m](https://s1.ax1x.com/2018/09/09/iiRR58.png)

<h2>项目介绍</h2>
<p>闲暇之时使用thinkphp和fly社区前端模板开发了一个论坛用来整理技能知识, 基本上满足一个论坛所需功能.欢迎使用和提出issue!</p>
<ul>
    <li>fly自带富文本编辑器</li>
    <li>PHPEmail激活邮件</li>
    <li>积分签到,发帖和回复消耗积分</li>
    <li>帖子收藏</li>
    <li>回帖和点赞排行榜</li>
    <li>后台RBAC权限管理</li>
    <li>帖子,用户和回复管理</li>
    <li>用户封禁</li>
</ul>

![前端展示页面](https://s1.ax1x.com/2018/09/09/iihnEt.png)

![后端展示页面](https://s1.ax1x.com/2018/09/09/iiheHI.png)

<h2>使用说明</h2>
<ul>
    <li>该项目支持PHP5.6以上和MySQL5.7</li>
    <li>请将项目内的所有文件直接放在根目录下；不要多层目录；例如正确：www/;错误：www/thinkbjy/</li>
    <li>管理员登录账号和密码默认为admin/123456</li>
    <li>配置在application/config-user.php, 需要修改成application/config.php</li>
    <li>数据库database-user.php也需要修改成database.php. 暂时不支持页面安装. 需要导入sql文件, 根目录tp_forum.sql文件</li>
</ul>

<h2>未来计划</h2>
<ul>
    <li style="text-decoration:line-through">路由注册(完成)</li>
    <li>安装论坛功能</li>
    <li>缓存Redis</li>
    <li>第三方登录: QQ, 微博</li>
    <li>手机验证短信</li>
    <li>论坛搜索帖子和用户</li>
</ul>

<h2>开源协议</h2>
<p>thinkphp-china遵循Apache2开源协议发布。Apache Licence是著名的非盈利开源组织Apache采用的协议。该协议和BSD类似，鼓励代码共享和尊重原作者的著作权，同样允许代码修改，再作为开源或商业软件发布。</p>