<?php

namespace Obblm\Core\Helper\Rule\Config;

use Symfony\Component\OptionsResolver\OptionsResolver;

class ConfigResolver
{
    /** @var OptionsResolver */
    private $resolver;
    /** @var ConfigResolver[] */
    private $children = [];
    public function __construct(ConfigInterface $configuration)
    {
        $this->resolver = new OptionsResolver();
        $this->configure($configuration);
    }
    private function configure($configuration)
    {
        $configuration->configureOptions($this->resolver);
        foreach ($configuration::getChildren() as $key => $childrenClass) {
            $children = new $childrenClass();
            if (!$children instanceof ConfigInterface) {
                $message = sprintf("The children must implements interface %s", ConfigInterface::class);
                throw new \Exception($message);
            }
            if ($children instanceof ConfigTreeInterface) {
                $this->children[$key] = new ConfigTreeResolver($children);
            } else {
                $this->children[$key] = new ConfigResolver($children);
            }
        }
    }
    public function resolve($data)
    {
        $resolved = $this->resolver->resolve($data);
        foreach ($this->children as $key => $children) {
            if ($children instanceof ConfigTreeResolver) {
                foreach ($resolved[$key] as $subKey => $subData) {
                    $resolved[$key][$subKey] = $children->resolve($subData);
                }
            } else {
                $resolved[$key] = $children->resolve($resolved[$key]);
            }
        }
        return $resolved;
    }
}
