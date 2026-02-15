<?php

namespace App\Traits;

use Illuminate\Support\Facades\App;

trait Translatable
{
    /**
     * Get translation relationship name
     */
    public function getTranslationRelationName()
    {
        return 'translations';
    }

    /**
     * Get translatable attributes
     */
    public function getTranslatableAttributes()
    {
        return $this->translatable ?? [];
    }

    /**
     * Get current locale
     */
    public function getCurrentLocale()
    {
        return App::getLocale();
    }

    /**
     * Get fallback locale
     */
    public function getFallbackLocale()
    {
        return config('app.fallback_locale', 'vi');
    }

    /**
     * Get translated attribute
     */
    public function getTranslatedAttribute($key, $locale = null)
    {
        $locale = $locale ?: $this->getCurrentLocale();
        
        // Try to get translation for requested locale
        $translation = $this->{$this->getTranslationRelationName()}()
            ->where('locale', $locale)
            ->first();
        
        if ($translation && !empty($translation->$key)) {
            return $translation->$key;
        }
        
        // Try fallback locale
        if ($locale !== $this->getFallbackLocale()) {
            $translation = $this->{$this->getTranslationRelationName()}()
                ->where('locale', $this->getFallbackLocale())
                ->first();
            
            if ($translation && !empty($translation->$key)) {
                return $translation->$key;
            }
        }
        
        // Return original attribute
        return $this->$key;
    }

    /**
     * Set translation for specific locale
     */
    public function setTranslation($locale, array $data)
    {
        return $this->{$this->getTranslationRelationName()}()->updateOrCreate(
            ['locale' => $locale],
            $data
        );
    }

    /**
     * Delete translation for specific locale
     */
    public function deleteTranslation($locale)
    {
        return $this->{$this->getTranslationRelationName()}()
            ->where('locale', $locale)
            ->delete();
    }

    /**
     * Has translation for specific locale
     */
    public function hasTranslation($locale)
    {
        return $this->{$this->getTranslationRelationName()}()
            ->where('locale', $locale)
            ->exists();
    }

    /**
     * Get all translations as array
     */
    public function getTranslationsArray()
    {
        return $this->{$this->getTranslationRelationName()}()
            ->pluck('locale')
            ->mapWithKeys(function ($locale) {
                $translation = $this->{$this->getTranslationRelationName()}()
                    ->where('locale', $locale)
                    ->first();
                
                $data = [];
                foreach ($this->getTranslatableAttributes() as $attribute) {
                    $data[$attribute] = $translation->$attribute ?? null;
                }
                
                return [$locale => $data];
            })
            ->toArray();
    }

    /**
     * Dynamically retrieve translated attributes
     */
    public function __get($key)
    {
        // Check if this is a translatable attribute
        if (in_array($key, $this->getTranslatableAttributes())) {
            return $this->getTranslatedAttribute($key);
        }
        
        // Check for "translated_" prefix
        if (str_starts_with($key, 'translated_')) {
            $attribute = substr($key, 11); // Remove "translated_" prefix
            if (in_array($attribute, $this->getTranslatableAttributes())) {
                return $this->getTranslatedAttribute($attribute);
            }
        }
        
        return parent::__get($key);
    }
}