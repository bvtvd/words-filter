<?php

namespace bvtvd;


class Filter
{
    /**
     * 文本
     * @var
     */
    protected $text;

    /**
     * 字典
     * @var
     */
    protected $dict;

    /**
     * blocker
     * @var string
     */
    protected $blocker = '*';

    protected $pattern;

    /**
     * special chars
     * @var string
     */
    protected $special = '\*|\.|\?|\+|\$|\^|\[|\]|\(|\)|\{|\}|\||\\\|\/';

    protected $callback;

    protected $model;
    /**
     * Filter constructor.
     * @param string $text
     * @param array $dict
     * @param string $blocker
     */
    public function __construct($text = '', $dict = [], $blocker = '*', $model = 'default')
    {
        $this->blocker($blocker);
        $this->text($text);
        $this->dict($dict);
        $this->model($model);
    }

    /**
     * 设置文本
     * @param $text
     * @return $this
     */
    public function text($text)
    {
        $this->text = $text;
        return $this;
    }

    /**
     * 设置脏词字典
     * @param array $dict
     * @return $this
     */
    public function dict($dict)
    {
        if(!is_array($dict)) throw new \Exception('Argument must be an Array');
        $this->dict = $dict;
        return $this;
    }

    /**
     * 设置替换文本
     * @param $blocker
     * @return $this
     */
    public function blocker($blocker)
    {
        $this->blocker = $blocker;
        return $this;
    }

    /**
     * 设置过滤模式
     * @param $model
     * @return $this
     */
    public function model($model)
    {
        $this->model = $model;
        return $this;
    }

    /**
     * 获取model
     * @return mixed
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * 检查是否存在脏词
     * @return bool
     */
    public function check()
    {
        if($this->text){
            $dict = $this->getSlicedDictionary();

            foreach ($dict as $slice){
                $pattern = join($this->filterSpecialChars($slice), '|');
                if(preg_match("/$pattern/i", $this->text)) return true;
            }
        }
        return false;
    }

    /**
     * 过滤关键字
     * @return null|string|string[]
     */
    public function clean()
    {
        if($callback = $this->getHandler()){
            $callback();
        }else{
            call_user_func([$this, $this->getModel() . 'Model']);
        }
        return $this->text;
    }

    /**
     * 默认处理方法
     */
    public function defaultModel()
    {
        $dict = array_fill_keys($this->dict, $this->blocker);
        $this->text = strtr($this->text, $dict);
    }

    /**
     * 正则处理方法
     */
    public function pregModel()
    {
        $dict = $this->filterSpecialChars($this->dict);

        $dict = array_chunk($dict, 1000);

        foreach ($dict as $slice){
            $pattern = join($slice, '|');
            $this->text = preg_replace_callback("/$pattern/i", function($matches){
                return str_repeat($this->blocker, mb_strlen($matches[0]));
            }, $this->text);
        }
    }

    /**
     * 获取分组字典
     * @return array
     */
    protected function getSlicedDictionary($size = 1000)
    {
        return array_chunk($this->dict, $size);
    }

    /**
     * 设置过滤处理函数
     * @param $callback
     * @throws Exception
     */
    public function setHandler($callback)
    {
        if(!$callback instanceof Closure) throw new \Exception('Argument must be an Closure');
        $this->callback = $callback->bindTo($this, __CLASS__);
    }

    /**
     * 获取自定义处理器
     * @return mixed
     */
    public function getHandler()
    {
        return $this->callback;
    }

    /**
     * 过滤正则特殊字符
     * @param $input
     * @return array|null|string|string[]
     */
    public function filterSpecialChars($input)
    {
        return is_array($input) ? $this->filterSpecialCharsArray($input) : $this->filterSpecialCharsString($input);
    }

    /**
     * 过滤数组中的特殊字符
     * @param $input
     * @return array
     */
    public function filterSpecialCharsArray($input)
    {
        return array_map(function($item){
            return preg_replace("/($this->special)/i", '\\\${1}', $item);
        }, $input);
    }

    /**
     * 过滤字符串中的特殊字符
     * @param $input
     * @return null|string|string[]
     */
    public function filterSpecialCharsString($input)
    {
        return preg_replace("/($this->special)/i", '\\\${1}', $input);
    }
}
