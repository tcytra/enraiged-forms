<?php

namespace Enraiged\Forms\Contracts;

use Enraiged\Forms\Builders\FormBuilder;
use Illuminate\Http\Request;

interface ProvidesForm
{
    /**
     *  Create and return a form builder instance against this model.
     *
     *  @param  \Illuminate\Http\Request  $request
     *  @return \Enraiged\Forms\Builders\FormBuilder
     */
    public function form(Request $request): FormBuilder;
    /*  Example implementation
    {
        $form = new ModelForm($request, $this);
    
        return $this->exists
            ? $form->edit('models.update', ['model' => $this->id])
            : $form->create('models.store');
    }
    */
}
