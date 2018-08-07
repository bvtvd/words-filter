敏感词过滤器, 支持中文

安装:
```
composer require bvtvd/words-filter
```

使用:
```php
use bvtvd\Filter\Filter;


$text = '你的观点很新颖, 但是从历史的维度看, 还存在狭隘的内部缺陷! to be or not to be, that's a question!';
$dict = ['观点', '内部缺陷', 'be'];

# 基本使用
$filter = new Filter($text, $dict);
echo $filter->clean();
//output: 你的**很新颖, 但是从历史的维度看, 还存在狭隘的****! to ** or not to **, that's a question!


# 自定义替换字符
$blocker = '@';
$filter->blocker($blocker);
echo $filter->clean();
//output: 你的@@很新颖, 但是从历史的维度看, 还存在狭隘的@@@@! to @@ or not to @@, that's a question!

# 也可以在初始化的时候直接设置替换字符
$filter = new Filter($text, $dict, $blocker);

#  设置文本
$filter->text($text);
# 设置敏感词
$fitler->dict($dict);

# 检查是否包含敏感词
$filter->check()
```
