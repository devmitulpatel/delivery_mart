<?php

namespace App\Actions;

use TCG\Voyager\Actions\AbstractAction;

class SendSMS extends AbstractAction
{
    public function getTitle()
    {
        return 'Send update';
    }

    public function getIcon()
    {
        return 'voyager-paper-plane';
    }

    public function getPolicy()
    {
        return 'read';
    }

    public function getAttributes()
    {
        return [
            'class' => 'btn btn-sm btn-success pull-right',
        ];
    }

    public function getDefaultRoute()
    {
        return route('sendSMS',$this->data->{$this->data->getKeyName()}) ;
    }

    public function shouldActionDisplayOnDataType()
    {
        return $this->dataType->slug == 'orders';
    }
}