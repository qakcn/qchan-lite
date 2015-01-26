如何设置config.php
==========
版本：v1.0

本文介绍如何设置*config.php*文件。

本指引使用Markdown语法书写，若使用文本编辑器查看，请注意正文中的格式标记，这些标记并不是正文的一部分。

以下示例只适用于使用文本编辑器查看：

    *表示说明* （两侧各有一个星号）
    **表示强调**（两侧各有两个星号）
    `表示代码`（两侧各有一个反引号，中间的内容才是代码内容，输入代码时请不要包括反引号）
    <表示链接>（两侧各有一个尖括号）

更多关于Markdown语法的说明，请参见<http://wowubuntu.com/markdown/>。

如果您使用Windows，可以使用[MarkdownEditor](https://github.com/jijinggang/MarkdownEditor)来查看本文档。下载地址：<https://github.com/jijinggang/MarkdownEditor/blob/master/download/MarkdownEditor.zip?raw=true>

如果您使用Mozilla Firefox，亦可安装扩展[Markdown Viewer](https://addons.mozilla.org/zh-CN/firefox/addon/markdown-viewer/)，然后直接用Firefox打开本文件。

设置格式
----------
所有设置都使用常量声明来完成，常量声明的格式是：

    define('常量名', 常量值);

在*config.php*中，常量名即设置项的名称，由大写字母和下划线组成。  
常量值可以是字符串、布尔值、整型、浮点型等标量类型。修改设置时，请**仅修改常量值的部分**。

* 字符串由英文单引号（'）或双引号（"）包围，本文件中使用单引号；
* 布尔值只有`true`或`false`，不区分大小写，本文件中使用小写（注意有的设置项虽然是true或false，但其值为字符串而不是布尔值，需要用引号包围）；
* 整型数值直接用十进制表示；浮点型用小数点分隔的两段十进制数字来表示。

设置说明
----------
下面对每一个设置项进行说明，包括设置项的作用和取值。


### UI_LANG
字符串值  
默认值：`'zh-CN'`

设置界面的默认语言，用户可以在界面右上角的语言切换自行切换成其他语言。  
取值为合法的ISO 639-1语言缩写，若使用default主题，目前仅支持`'en'`（英语）、`'zh-CN'`（中文（中国））和`'ja'`（日本语）；若使用其他主题，以主题的说明为准。关于翻译事宜，请联系<qakcnyn@gmail.com>


### UI_THEME
字符串值  
默认值：`'default'`

设置主界面所使用的主题，该主题必须在*themes*目录下。


### SITE_TITLE
字符串值

设置网站的标题，显示在浏览器的标题栏中。


### SITE_DESCRIPTION
字符串值

设置网站的说明，包含在网站头部的`<meta>`元素中。


### SITE_KEYWORDS
字符串值

设置网站的关键字，包含在网站头部的`<meta>`元素中。


### ADMIN_EMAIL
字符串值

管理员的Email地址，会包含在页面的反馈信息中。

### MAIN_SITE
布尔值  
默认值：`false`

是否启用主站链接。启用后会在default主题的左下角显示，其他主题参见主题说明。


### MAIN_SITE_NAME
字符串值

主站的名称。


### MAIN_SITE_LOGO
字符串值

主站的LOGO图片的URL。


### MAIN_SITE_URL
字符串值

主站的URL。


### COPYRIGHT
字符串值

这是在页面底部的版权声明，可以使用HTML代码。


### SERVICE
字符串值  
默认值：`'tietuku'`

设置图床使用的存储服务提供商，目前仅支持贴图库`'tietuku'`。


### ALBUM_STRATEGY
字符串值  
默认值：`'single'`

设置相册使用的策略。设置为`'single'`则使用指定的相册；设置为`'monthly'`则每个月生成一个相册。


### SINGLE_ALBUM
整型数值  
默认值：`0`

当`ALBUM_STRATEGY`为`'single'`时所使用的相册的ID。


### ALBUM_PREFIX
字符串值  
默认值：`'qchan'`

当`ALBUM_STRATEGY`为`'monthly'`时相册名称的前缀。


### DIRECT_AJAX
布尔值  
默认值：`true`

设置为`true`时通过AJAX直接上传到存储服务提供商；`false`时先上传到Qchan所在服务器，再从服务器上传到存储服务提供商。


### TIETUKU_ACCESSKEY
字符串值  

贴图库提供的Access Key，请到[贴图库开放平台](http://open.tietuku.com)，登录后在*管理中心*下的*密钥*页面获取。


### TIETUKU_SECRETKEY
字符串值  

贴图库提供的Secret Key，请到[贴图库开放平台](http://open.tietuku.com)，登录后在*管理中心*下的*密钥*页面获取。