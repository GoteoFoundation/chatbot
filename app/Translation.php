<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasCompositePrimaryKeyTrait;

class Translation extends Model
{
    use HasCompositePrimaryKeyTrait;

    protected $fillable = ['translation_id', 'language_id', 'term'];
    protected $primaryKey = ['translation_id', 'language_id'];
    public $incrementing = false;
    public $timestamps = false;

    /**
     * Get the language that owns the translation.
     */
    public function language()
    {
        return $this->belongsTo('App\Language');
    }

    /**
     * Check if is the main translation.
     *
     * @return boolean
     */
    public function isMainTranslation()
    {
        return $this->language->isMainLanguage();
    }

    /**
     * Check if is the translation belongs the given language.
     *
     * @return boolean
     */
    public function belongedByLanguage($languageId)
    {
        return $this->language->id == $languageId;
    }

    /**
     * Returns the term.
     *
     * @return string
     */
    public function getTerm()
    {
        return $this->term;
    }

    /**
     * Returns the term.
     *
     * @param string
     */
    public function setTerm($term)
    {
        $this->term = $term;
    }
}
