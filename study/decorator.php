<?php
/**
 * Created by PhpStorm.
 * User: think
 * Date: 31/07/2017
 * Time: 10:05 PM
 */
interface Decorator
{
    public function display();
}
class XiaoFang implements Decorator
{
    private $name;
    public function __construct($name)
    {
        $this->name = $name;
    }
    public function display()
    {
        echo "I am ".$this->name.", I am going out! <br>";
    }
}
class Finery implements Decorator
{
    private $component;
    public function __construct(Decorator $component)
    {
        $this->component = $component;
    }
    public function display()
    {
        $this->component->display();
    }
}
class Shoes extends Finery
{
    public function display()
    {
        echo "Put on shoes <br>";
        parent::display();
    }
}
class Skirts extends Finery
{
    public function display()
    {
        echo "Put on skirts <br>";
        parent::display();
    }
}
class Fires extends Finery
{
    public function display()
    {
        echo "Fire hear before going out <br>";
        parent::display();
        echo "Fire hear again after going out <br>";
    }
}

$xiaoFang = new XiaoFang('Xiao Fang');
$shoes = new Shoes($xiaoFang);
$skirts = new Skirts($shoes);
$fires = new Fires($skirts);
$fires->display();