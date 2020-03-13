<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasCompositePrimaryKeyTrait;

class Copy extends Model
{
    use HasCompositePrimaryKeyTrait;

    protected $fillable = ['name', 'value', 'language_id'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['language_id', 'created_at', 'updated_at'];

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $visible = ['name', 'value'];

    protected $primaryKey = ['name', 'language_id'];

    public $incrementing = false;
}
