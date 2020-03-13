<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    protected $fillable = ['name', 'iso_code', 'is_parent', 'language_id'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['language_id', 'is_parent', 'created_at', 'updated_at'];

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $visible = ['id', 'name'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'is_parent' => 'boolean',
    ];

    /**
     * Check if is the main language.
     *
     * @return boolean
     */
    public function isMainLanguage()
    {
        return $this->is_parent;
    }

    /**
     * Converts language to array for datatable.
     */
    public function toDatatable()
    {
        $this->name .= view('languages.datatable.name', ['language' => $this])->render();

        return $this->append(['actions'])
            ->makeVisible(['actions'])
            ->toArray();
    }

    /**
     * Converts language to array for select2.
     */
    public function toSelect2()
    {
        return $this->setAppends(['text'])
            ->makeVisible(['text'])
            ->toArray();
    }

    /**
     * Get the datatable actions.
     */
    public function getActionsAttribute()
    {
        return view('languages.datatable.actions', ['language' => $this])->render();
    }

    /**
     * Get the text of the language.
     */
    public function getTextAttribute()
    {
        return $this->name;
    }

    /**
     * Get the support language.
     */
    public function supportLanguage()
    {
        return $this->belongsTo('App\Language', 'language_id', 'id');
    }

    /**
     * Get the copies for the language.
     */
    public function copies()
    {
        return $this->hasMany('App\Copy');
    }

    /**
     * Get the translations for the language.
     */
    public function translations()
    {
        return $this->hasMany('App\Translation');
    }

    /**
     * Get the copy value by key for the language.
     * @param $key
     * @return string
     */
    public function getCopy($key)
    {
        return optional($this->copies->first(function ($copy) use ($key) {
            return $copy->name == $key;
        }))->value;
    }
}
