<?php

namespace Enraiged\Forms\Builders;

use Enraiged\Builders\Secure\AssertSecure;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

abstract class FormBuilder
{
    use Traits\BuilderConstructor,
        Traits\FormActions,
        Traits\FormFields,
        Traits\FormModel,
        Traits\PrepareFields,
        Traits\SanityChecks,
        AssertSecure;

    /**
     *  Return a form builder instance for 'create' from a model and resource definitions.
     *
     *  @param  string  $route
     *  @param  string  $method = 'post'
     *  @return $this
     */
    public function create($route, $method = 'post')
    {
        $this->route = $this->checkRouteExists($route);

        return $this->router($method);
    }

    /**
     *  Return a form builder instance for 'edit' from a model and resource definitions.
     *
     *  @param  string  $route
     *  @param  array   $parameters
     *  @param  string  $method = 'patch'
     *  @return $this
     */
    public function edit($route, $parameters, $method = 'patch')
    {
        $this->route = $this->checkRouteExists($route);

        return $this->router($method, $parameters);
    }

    /**
     *  Determine the routing for this form.
     *
     *  @param  string  $method
     *  @param  array   $parameters = []
     *  @return $this
     */
    private function router(string $method, array $parameters = [])
    {
        preg_match('/\{[a-z]+\}/', $this->route->uri, $matches);

        if (count($matches)) {
            if ($this->request()->route()->hasParameters()) {
                $parameters = $this->request()->route()->parameters();
            }

            $this->route->parameterNames = preg_replace('/[\{\}]/', '', $matches);
            $this->route->parameters = collect($parameters)
                ->only($this->route->parameterNames)
                ->toArray();
        }

        $action = $this->model->exists
            ? 'update'
            : 'store';

        if (!Auth::check() || $this->request()->user()->can($action, $this->model)) {
            $this->resource = [
                'action' => $action,
                'api' => preg_match('/^api/', $this->route->uri) === 1 ? true : false,
                'method' => $method,
                'name' => $this->route->getName(),
                'params' => $this->route->parameters,
                'url' => route(
                    $this->route->getName(),
                    $this->route->parameters,
                    config('enraiged.app.absolute_uris')
                ),
            ];
        }

        return $this;
    }

    /**
     *  Return the assembled table template.
     *
     *  @return array
     */
    public function template(): array
    {
        return [
            'actions' => collect($this->actions)
                ->only(['back', 'clear', 'reset', $this->resource['action']])
                ->transform(fn ($action, $index)
                    => $index === $this->resource['action'] ? $this->resource : $action)
                ->toArray(),
            'class' => $this->class,
            'fields' => $this->fields,
            'labels' => $this->labels,
            'referer' => $this->referer,
            'resource' => $this->resource,
        ];
    }

    /**
     *  Create and return a builder from the request and optional parameters.
     *
     *  @param  \Illuminate\Http\Request  $request
     *  @param  \Illuminate\Database\Eloquent\Model  $model
     *  @return \Enraiged\Forms\Builders\FormBuilder
     *  @static
     */
    public static function From(Request $request, Model $model): self
    {
        $called = get_called_class();

        return new $called($request, $model);
    }
}
