# Account

一个很简单的在线的账号管理系统

## 为什么要使用一个在线账号管理系统?

账号太多记不住?  
本地记录的账号文件七零八碎,老是要翻很久?  
放到别人的系统中不放心?  
那么这款在线账号管理系统就适合你了。只需要简单的安装部署,就可以轻松管理所有的账号。  
使用中有问题可以@me, QQ: 61470255, 邮件: tpyzlxy@163.com

## 引用的项目和技术

[phalcon](https://github.com/phalcon/cphalcon)  
[layui](https://github.com/sentsin/layui)

## 安装步骤

1、安装lnmp, 请参照[lnmp的安装方法](https://lnmp.org/install.html)  
1、安装phalcon, 请参照[phalcon的安装方法](https://github.com/phalcon/cphalcon/blob/master/README.md#get-started)  
2、下载本项目并安装
  
```bash
#进入需要放置项目的站点目录
cd /data/wwwroot
git clone https://github.com/fesiong/account.git
cd account
#创建数据库文件
cp app/config/site.dist.php app/config/site.php
#修改数据库连接,改为您的数据库连接信息
vi app/config/site.php
#导入数据库文件
mysql -u root -p
create schema account;
use account;
source /data/wwwroot/account/account.sql
```
至此,你已经安装完毕了, 你可以通过url访问到项目。  
默认的账号密码分别是:  
账号: 12345678901  
密码:123456  
授权码:123456  

## 注意事项

当表结构有更改后,需要手动清空app/cache/metaData文件夹里的表缓存文件  
当前端静态页面有更改后,需要清理浏览器缓存,才能看到最新的页面状态  

## 可能会遇到的错误的处理方法

错误:  
```bash
Warning: realpath(): open_basedir restriction in effect. File(/data/wwwroot/account) is not within the allowed path(s): 
```
打开/usr/local/nginx/conf/fastcgi.conf,修改
```ini
fastcgi_param PHP_ADMIN_VALUE "open_basedir=$document_root/:/tmp/:/proc/";

```
为  
```ini
fastcgi_param PHP_ADMIN_VALUE "open_basedir=$document_root/:$document_root/../:/tmp/:/proc/";

```

## License

Account is open source software licensed under the New BSD License. See the [LICENSE.txt](LICENSE.txt) file for more.