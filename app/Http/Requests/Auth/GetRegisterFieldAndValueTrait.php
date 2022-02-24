<?php

namespace App\Http\Requests\Auth;

use SiteHelper;

trait GetRegisterFieldAndValueTrait
{
    public function getFieldName()
    {
        return $this->has('email') ? 'email' : 'mobile';
    }

    public function getFieldValue()
    {
        $field = $this->getFieldName();
        $value = $this->input($field);

        if ($field === 'mobile') {
            $value = SiteHelper::toValidMoibileNumber($value);
        }

        return $value;
    }
}
