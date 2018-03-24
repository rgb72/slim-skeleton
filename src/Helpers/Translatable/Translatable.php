<?php

namespace App\Helpers\Translatable;

use App\Singletons\GlobalVariable;

trait Translatable {

    protected $locale = null;
    protected $translation_class_suffix = 'Translation';
    protected $_locale_field = 'locale';

    protected function mutateAttributeForArray($key, $value) {
        $fields = $this->getTranslationFields();
        if(in_array($key, $fields)) {
            if(is_null($this->translation)) return null;

            $translation = $this->translation->locale !== $this->getLocale() ||
                            ($this->isHasDevice() && $this->translation->device !== $this->getDevice())
                                ? $this->translation()->first()
                                : $this->translation;

            $value = is_null($translation) ? null : $translation->{$key};

            $this->resetLocale();
            if($this->isHasDevice()) $this->resetDevice();

            return $value;
        }

        return parent::mutateAttributeForArray($key, $value);
    }

    public function __get($name) {
        $fields = $this->getTranslationFields();
        if(in_array($name, $fields)) {
            if(!$this->translation) return null;

            $translation = $this->translation->locale !== $this->getLocale() ||
                            ($this->isHasDevice() && $this->translation->device !== $this->getDevice())
                                ? $this->translation()->first()
                                : $this->translation;

            $value = is_null($translation) ? null : $translation->{$name};

            $this->resetLocale();
            if($this->isHasDevice()) $this->resetDevice();

            return $value;
        }

        $translations = $this->getAvaliableTranslations();
        if(in_array($name, $translations)) {

            $locale_field = $this->getLocaleField();
            $locale = $this->getLocale();

            return $this->translations()->where($locale_field, $locale)->get();
        }

        return parent::__get($name);
    }

    public function __call($name, $property) {
        $translations = $this->getAvaliableTranslations();
        if(in_array($name, $translations)) {

            $locale_field = $this->getLocaleField();
            $locale = $this->getLocale();

            return $this->translations()->where($locale_field, $locale);
        }

        $fields = $this->getTranslationFields();
        if(in_array($name, $fields)) {
            $translation = $this->translation()->first();

            if(is_null($translation)) return null;

            $value = $translation->{$name};

            $this->resetLocale();
            if($this->isHasDevice()) $this->resetDevice();

            return $value;
        }

        return parent::__call($name, $property);
    }

    public function translation() {
        $translation_class = $this->getTranslationClass();
        $foreign_key       = $this->getTranslationForeignKey();
        $local_key         = $this->getTranslationLocalKey();

        $locale_field      = $this->getLocaleField();
        $locale            = $this->getLocale();

        $relationship = $this->hasOne($translation_class, $foreign_key, $local_key)->where($locale_field, $locale);

        if($this->isHasDevice()) {
            $device = $this->getDevice();
            $device_field = $this->getDeviceField();
            $relationship = $relationship->where($device_field, $device);
        }

        return $relationship;
    }

    public function translations() {
        $translation_class = $this->getTranslationClass();
        $foreign_key       = $this->getTranslationForeignKey();
        $local_key         = $this->getTranslationLocalKey();

        $relation = $this->hasMany($translation_class, $foreign_key, $local_key)
                            ->orderBy($this->getLocaleField(), 'desc');

        if($this->isHasDevice()) $relation = $relation->orderBy($this->getDeviceField());

        return $relation;
    }

    public function translate($locale = null) {
        if(!is_null($locale)) $this->locale = $locale;
        return $this;
    }

    public function getTranslationFields() {
        return $this->translation_fields ?: [];
    }

    public function getTranslationClass() {
        return property_exists($this, 'translation_class') ? $this->translation_class : get_class($this).$this->translation_class_suffix;
    }

    public function getTranslationForeignKey() {
        return property_exists($this, 'translation_key') ? $this->translation_key : $this->getForeignKey();
    }

    public function getTranslationLocalKey() {
        return property_exists($this, 'translation_local_key') ? $this->translation_local_key : $this->primaryKey;
    }

    public function getLocaleField() {
        return property_exists($this, 'locale_field') ? $this->locale_field : $this->_locale_field;
    }

    public function getLocale() {
        return $this->locale ?: $this->getDefaultLocale();
    }

    public function getAvaliableTranslations() {
        return property_exists($this, 'avaliable_translations') ? $this->avaliable_translations : $this->getDefaultAvaliableTranslations();
    }

    public function isHasDevice() {
        return in_array(HasDevice::class, class_uses($this));
    }

    protected function resetLocale() {
        $this->locale = null;
    }

    private function getDefaultLocale() {
        $variables = GlobalVariable::getInstance();
        return $this->default_locale ?: $variables->current_lang ?: getenv('LANG_DEFAULT');
    }

    private function getDefaultAvaliableTranslations() {
        $avaliable_translations = getenv('LANG_AVALIABLE');
        $avaliable_translations = array_map('trim', explode(',', $avaliable_translations));

        return $avaliable_translations;
    }

}
