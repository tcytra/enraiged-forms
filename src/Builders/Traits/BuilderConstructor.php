<?php

namespace Enraiged\Forms\Builders\Traits;

use Enraiged\Collections\RequestCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

trait BuilderConstructor
{
    /** @var  object  The templated form classes. */
    protected $class;

    /** @var  object  The templated form labels direction. */
    protected $labels;

    /** @var  string  The initial request http_referer. */
    protected $referer;

    /** @var  object  The http request. */
    protected $request;

    /** @var  object  The form action configuration. */
    protected $resource;

    /** @var  \Illuminate\Routing\Route  The form route. */
    protected $route;

    /** @var  bool  Whether or not this form is tabbed. */
    protected $tabbed;

    /** @var  string  The master template json file path. */
    protected $template;

    /**
     *  Create an instance of the FormBuilder.
     *
     *  @param  \Illuminate\Http\Request  $request
     *  @return void
     */
    public function __construct(Request $request, Model $model)
    {
        $this->model = $model;

        $this->referer = $request->server('HTTP_REFERER');

        $this->request = RequestCollection::from($request);

        $this->load(config('enraiged.forms.template'));

        if ($this->template && $template = $this->parse($this->template)) {
            $this->load($template);
        }

        $this->prepare();
    }

    /**
     *  Load a provided array of builder parameters.
     *
     *  @param  array   $parameters
     *  @return $this
     */
    public function load(array $parameters)
    {
        foreach ($parameters as $parameter => $content) {
            if (property_exists($this, $parameter)) {
                $this->{$parameter} = $content;
            }
        }

        return $this;
    }

    /**
     *  Parse a json template from a provided argument.
     *
     *  @param  string  $value
     *  @return array|null
     */
    public function parse($value): ?array
    {
        $object = substr($value, 0, 1) !== '{' && File::exists($value)
            ? file_get_contents($value)
            : $value;

        if (substr($object, 0, 1) === '{') {
            return json_decode($object, true);
        }

        return null;
    }

    /**
     *  Return the request.
     *
     *  @return \Illuminate\Http\Request
     */
    public function request()
    {
        return $this->request;
    }
}
