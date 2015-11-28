<?php
/**
 * Created by PhpStorm.
 * User: orbisnull
 * Date: 27.11.2015
 * Time: 18:44
 */

namespace Image\Model;


use DeltaCore\Parts\Configurable;

class Processor
{
    use Configurable;

    public function getTemplates()
    {
        return $this->getConfig(["templates"]);
    }

    public function getTemplate($name)
    {
        $template = $this->getConfig(["templates", $name]);
        return $template ? $template->toArray() : null;
    }

    public function process($source, $output, $template)
    {
        $template = $this->getTemplate($template);
        if (null === $template) {
            throw new \RuntimeException("Template $template not exist");
        }
        $image = new Image($source);
        foreach($template as  $key=>$value) {
            if (is_integer($key)) {
                $key = $value;
                $value = [];
            }
            if (is_array($value)) {
                call_user_func_array([$image, $key], $value);
            } else {
                if (is_callable($value)) {
                    $value = call_user_func($value, $this->getConfig());
                }
                call_user_func([$image, $key], $value);
            }
        }
        $image->write($output);
        return $output;
    }
}
