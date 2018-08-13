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

    /**
     * Filter constructor.
     * @param string $text
     * @param array $dict
     * @param string $blocker
     */
    public function __construct($text = '', $dict = [], $blocker = '*')
    {
        $this->text($text);
        $this->dict($dict);
        $this->blocker($blocker);
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
        $this->dict = array_map(function($item){
            // handler special chars
            return preg_replace("/($this->special)/i", '\\\${1}', $item);
        }, $dict);
        $this->pattern = join($this->dict, '|');
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
     * 检查是否存在脏词
     * @return bool
     */
    public function check()
    {
        if($this->text){
            return boolval(preg_match("/$this->pattern/i", $this->text));
        }
        return false;
    }

    /**
     * 过滤关键字
     * @return null|string|string[]
     */
    public function clean()
    {
        return preg_replace_callback("/$this->pattern/i", function($matches){
            return str_repeat($this->blocker, mb_strlen($matches[0]));
        }, $this->text);
    }

}
