<?php

// 界面设置
define('UI_LANG', 'zh-CN'); //设置界面默认语言
define('UI_THEME', 'default'); //设置界面默认主题

// 站点信息
define('SITE_TITLE', 'Qchan图床'); // 网站标题
define('SITE_DESCRIPTION', '上传与分享'); // 网站描述
define('SITE_KEYWORDS', 'images, photos, image hosting, photo hosting, free image hosting'); //网站关键字
define('ADMIN_EMAIL', 'admin@example.com'); // 管理员Email
// 主站设置
define('MAIN_SITE', false); // 是否显示主站链接
define('MAIN_SITE_NAME', ''); // 主站名称
define('MAIN_SITE_LOGO', ''); // 主站logo地址
define('MAIN_SITE_URL', ''); // 主站URL

// 版权声明
define('COPYRIGHT', '所有未声明版权图片均需在<a rel="license" href="http://creativecommons.org/licenses/by-nc-sa/4.0/">知识共享 署名-非商业使用-相同方式共享 4.0 协议</a>下发布。');

// 服务设置
define('SERVICE', 'tietuku'); // 服务提供商：目前仅支持'tietuku'（贴图库）
define('ALBUM_STRATEGY', 'single'); // 相册使用策略，'single'所有照片存入一个相册，'monthly'每个月一个相册
define('SINGLE_ALBUM', 0); // 'single‘策略所使用的相册ID，ALBUM_STRATEGY设置为'monthly'时无效
define('ALBUM_PREFIX', 'qchan'); // 'monthly'策略的相册前缀，ALBUM_STRATEGY设置为'single'时无效
define('DIRECT_AJAX', false); // 设置为true时直接上传到服务提供商，不经过本程序的服务器

// 贴图库API设置
// 下面两个Key请到[贴图库开放平台](http://open.tietuku.com)->(登录后)[管理中心]->[密钥]获取
define('TIETUKU_ACCESSKEY', ''); // 贴图库API的AccessKey
define('TIETUKU_SECRETKEY', ''); // 贴图库API的SecretKey
