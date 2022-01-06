<?php
/**
 * Created by IntelliJ IDEA.
 * User: wdp
 * Date: 2017/5/26
 * Time: 11:32
 */

namespace senyzy\wechat;


abstract class Base
{
    /**
     * 微信组件
     *
     * @var Wechat
     */
    protected $wechat;

    /**
     * @param Wechat $wechat
     */
    public function __construct($wechat)
    {
        $this->wechat = $wechat;
    }
}