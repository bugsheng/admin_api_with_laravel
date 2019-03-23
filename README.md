## Admin Api With Laravel ##

<p>本项目属于一个用于基础后台管理-前后端分离后端工程项目中的后端管理工程（即API接口工程）。
<br/>
为了能有一个属于自己的一套基于Laravel框架的后台管理API接口解决基础方案。
<br/>
本项目基于[Laravel](https://github.com/laravel/laravel) 5.7版本。</p>


## 环境即安装 ##

### 环境要求 ###

* PHP >= 7.1.3
* OpenSSL PHP 扩展
* PDO PHP 扩展
* Mbstring PHP 扩展
* Tokenizer PHP 扩展
* XML PHP 扩展
* Ctype PHP 扩展
* JSON PHP 扩展
* Mysql >= 5.7.*
* Redis

### 安装 ###

* `git clone https://github.com/shengl-php/admin_api_with_laravel.git projectname`
* `cd projectname`
* `composer install`
* `php artisan key:generate`
* Create a database and inform *.env*
* `php artisan migrate --seed` to create and populate tables
* Inform *config/mail.php* for email sends
* `php artisan vendor:publish` to publish filemanager
* `php artisan serve` to start the app on http://localhost:8000/



## 关于本项目 ##

### 项目计划 ###

1. 搭建基础配置内容
2. 构建项目架构模式体系
3. 构建基础用户登录，登出，个人信息修改接口
4. 分支~rbac -构建后台管理系统前后端RBAC模块接口
5. 分支~thirdPart User -用户第三方授权绑定及登录
6. 。。。待续

