<?php

class ProviderReview extends Eloquent {

    protected $table = 'review_provider';

    public function dog()
    {
        return $this->belongsTo('Dog');
    }

}
