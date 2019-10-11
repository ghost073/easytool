# easytool
工具封装 tool library

#

##目录说明
1. samples是每个工具类的demo, 实用方法参照demo中操作
2. 测试用例执行方法 easytool目录下 
3. 需要打印的数据都是用 @group printdata, 需要单独按分组测试

(1) 测试目录下的所有test* 方法  
vendor/bin/phpunit samples/date/DateTest.php --exclude-group printdata  
或  
vendor/bin/phpunit samples\\date\\DateTest --exclude-group printdata

测试需要打印数据的分组
vendor/bin/phpunit samples/date/DateTest.php --group printdata  

(2) 测试某个方法   
vendor/bin/phpunit --filter showFutureDayArr samples/date/DateTest.php

#Elasticsearch
Elasticsearch  Elasticsearch基本操作的封装基于 es7.3版本

#Date
Date 日期相关格式化操作

#Tool
实用工具类

#Validate
常规验证类操作

