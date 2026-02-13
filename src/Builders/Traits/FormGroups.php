<?php

namespace Enraiged\Forms\Builders\Traits;

trait FormGroups
{
    /**
     *  Set or return a section postcontent.
     *
     *  @param  string  $name
     *  @param  string|null  $body = null
     *  @param  string|null  $class = null
     */
    public function contextual(string $name, string $param, $body = null, $class = null) {
        $content = $this->field("{$name}::{$param}");

        if ($body) {
            if ($class || (is_array($content) && key_exists('class', $content))) {
                $class = $class ?: (is_array($content) && key_exists('class', $content)
                    ? $content['class']
                    : null);

                $name = "{$name}::{$param}";
                $params = ['body' => $body, 'class' => $class];

            } else {
                $params = [$param => $body];
            }

            return $this->field($name, $params);
        }

        return $content;
    }

    /**
     *  Set or return a section precontent.
     *
     *  @param  string  $name
     *  @param  string|null  $body = null
     *  @param  string|null  $class = null
     */
    public function precontent(string $name, $body = null, $class = null)
    {
        return $this->contextual($name, 'precontent', $body, $class);
    }

    /**
     *  Set or return a section postcontent.
     *
     *  @param  string  $name
     *  @param  string|null  $body = null
     *  @param  string|null  $class = null
     */
    public function postcontent(string $name, $body = null, $class = null)
    {
        return $this->contextual($name, 'postcontent', $body, $class);
    }
}
