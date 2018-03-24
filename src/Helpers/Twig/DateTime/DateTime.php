<?php

namespace App\Helpers\Twig\DateTime;

use Carbon\Carbon;
use Symfony\Component\Yaml\Yaml;
use Exception;

class DateTime {

    protected $datetime;
    protected $format = 'Y-m-d';
    protected $lang;

    // format that don't need to replace
    private $digi_format = ['d', 'j', 'N', 'w', 'z', 'm', 'n', 't', 'L', 'B', 'g', 'G', 'h', 'H', 'i', 's'];

    public function __construct($datetime) {
        if($datetime instanceof Carbon) $this->datetime = $datetime;
        else $this->datetime = Carbon::parse($datetime);
    }

    public function format($format) {
        $this->format = $format;
        return $this;
    }

    public function lang($lang) {
        $this->lang = $lang;

        $file = sprintf('%s/Lang/%s.yaml', __DIR__, $this->lang);
        if(realpath($file)) {
            $this->format_lang = function_exists('yaml_parse_file') ?
                                    yaml_parse_file($file) :
                                    Yaml::parse(file_get_contents($file));
        }

        return $this;
    }

    public function get() {
        if(is_null($this->lang) || $this->lang === 'en') return $this->datetime->format($this->format);
        if(!file_exists(__DIR__.'/Lang/'.$this->lang.'.yaml')) throw new Exception('Language is not found in '.__DIR__.'/Lang');

        $format = preg_replace('/([a-zA-Z])/', '{$1}', $this->format);

        $format = $this->replaceDigitalFormat($format);

        if(!preg_match_all('/{(D|l|M|F|Y|y)}/', $format, $match))
            return $format;

        $format = $this->replaceDayFormat($format, $match[1]);
        $format = $this->replaceMonthFormat($format, $match[1]);
        $format = $this->replaceYearFormat($format, $match[1]);

        return $format;
    }

    protected function replaceDigitalFormat($format) {
        foreach ($this->digi_format as $value) {
            $search  = '{'.$value.'}';
            $replace = $this->datetime->format($value);
            $format  = str_replace($search, $replace, $format);
        }

        return $format;
    }

    protected function replaceDayFormat($format, $match) {
        if (!!array_intersect(['D', 'l'], $match)) {
            $dw = $this->datetime->format('w');

            $search = ['{D}', '{l}'];
            $replace = [$this->format_lang['D'][$dw], $this->format_lang['l'][$dw]];
            $format = str_replace($search, $replace, $format);
        }

        return $format;
    }

    protected function replaceMonthFormat($format, $match) {
        if (!!array_intersect(['M', 'F'], $match)) {
            $m = $this->datetime->format('n') - 1;

            $search = ['{M}', '{F}'];
            $replace = [$this->format_lang['M'][$m], $this->format_lang['F'][$m]];
            $format = str_replace($search, $replace, $format);
        }

        return $format;
    }

    protected function replaceYearFormat($format, $match) {
        if (!!array_intersect(['Y', 'y'], $match)) {
            $y = (int)$this->datetime->format('Y') + (int)$this->format_lang['year'];

            $search = ['{Y}', '{y}'];
            $replace = [$y, substr($y, -2)];
            $format = str_replace($search, $replace, $format);
        }

        return $format;
    }

}
