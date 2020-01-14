<?php

namespace Vientodigital\LaravelForum\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->setTable(config('laravel-forum.table_names.settings', 'settings'));
    }

    protected $fillable = ['key', 'value'];
}
