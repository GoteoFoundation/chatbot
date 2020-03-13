<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Topic extends Model
{
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['created_at', 'updated_at'];

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $visible = ['id', 'name'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name'];

    /**
     * Get the datatable actions.
     */
    public function getActionsAttribute()
    {
        return view('topics.datatable.actions', ['topic' => $this])->render();
    }

    /**
     * Get the text of the topic.
     */
    public function getTextAttribute()
    {
        return $this->name;
    }

    /**
     * Converts topic to array for datatable.
     */
    public function toDatatable()
    {
        return $this->append(['actions'])
            ->makeVisible(['actions'])
            ->toArray();
    }

    /**
     * Converts topic to array for select2.
     */
    public function toSelect2()
    {
        return $this->setAppends(['text'])
            ->makeVisible(['text'])
            ->toArray();
    }

    /**
     * Get the questions for the topic.
     */
    public function questions()
    {
        return $this->hasMany('App\Question');
    }
}
