<?php

namespace Enraiged\Forms\Builders\Traits;

use Illuminate\Support\Str;

trait FormActions
{
    /** @var  array  The templated form actions. */
    protected $actions;

    /**
     *  Get or set the form actions.
     *
     *  @param  array|null  $actions = null
     *  @param  bool|null   $merge = false
     *  @return array|self
     */
    public function actions(array $actions = null, bool $merge = false)
    {
        if ($actions) {
            $this->actions = $merge
                ? [...$this->actions(), ...$actions]
                : $actions;

            return $this;
        }

        return $this->actions;
    }

    /**
     *  Prepare and return an action from the provided parameters.
     *
     *  @param  string  $action
     *  @param  array   $parameters
     *  @return array
     */
    protected function prepareAction($action, $parameters): array
    {
        $method = Str::camel("prepare_{$action}_action");

        return method_exists($this, $method)
            ? $this->{$method}($parameters)
            : $parameters;
    }

    /**
     *  Prepare the actions provided for this form builder.
     *
     *  @param  array|null  $actions = null
     *  @param  bool|null   $merge = false
     *  @return $this
     */
    protected function prepareActions(array $actions = null, bool $merge = false)
    {
        if ($actions) {
            $this->actions($actions, $merge);
        }

        $this->actions = collect($this->actions)
            ->transform(fn ($action, $index) => $this->prepareAction($index, $action))
            ->toArray();

        return $this;
    }
}
