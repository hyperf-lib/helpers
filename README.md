# 介绍
基于hyperf框架，封装日常使用辅助类；主要包含日志调用、Redis调用、Redis分布式锁、TraceId和常用函数等。

# 安装方式
```
composer require hyperf-lib/helpers
```

# 常用函数
获取容器实例
```
di(\Redis::class); 
```
分转元，元转分
```
fen2yuan(100);
yuan2fen(1.00);
```
分布式锁
```
runWithLock($callback, $lockName, $lockSec, $waitSec);
```
API返回格式
```
apiSucc($data);
apiErr($code, $message);
```
分布式ID获取
```
getPkId();
```
异常堆栈信息格式化
```
formatThrowable($throwable);
```
Redis调用示例
```
Helpers\Redis::set('demo', 'demo');
```
日志调用示例
```
Helpers\Log::info(__FUNCTION__, []);
# 统一日志格式，可以将app/Listener/DbQueryExecutedListener.php:58
$this->logger->info(sprintf('[%s] %s', $event->time, $sql));
#替换为
Helpers\Log::info(sprintf('[%s] %s', $event->time, $sql));
```