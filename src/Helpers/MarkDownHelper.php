<?php

namespace App\Helpers;

class MarkDownHelper
{
    public static function transliterate(string $text): string
    {
        $cyrillicToLatin = [
            'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'h', 'ґ' => 'g', 'д' => 'd',
            'е' => 'e', 'є' => 'ie', 'ж' => 'zh', 'з' => 'z', 'и' => 'y', 'і' => 'i',
            'ї' => 'i', 'й' => 'i', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n',
            'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't', 'у' => 'u',
            'ф' => 'f', 'х' => 'kh', 'ц' => 'ts', 'ч' => 'ch', 'ш' => 'sh', 'щ' => 'shch',
            'ь' => '', 'ю' => 'iu', 'я' => 'ia',
            'А' => 'A', 'Б' => 'B', 'В' => 'V', 'Г' => 'H', 'Ґ' => 'G', 'Д' => 'D',
            'Е' => 'E', 'Є' => 'Ie', 'Ж' => 'Zh', 'З' => 'Z', 'И' => 'Y', 'І' => 'I',
            'Ї' => 'I', 'Й' => 'I', 'К' => 'K', 'Л' => 'L', 'М' => 'M', 'Н' => 'N',
            'О' => 'O', 'П' => 'P', 'Р' => 'R', 'С' => 'S', 'Т' => 'T', 'У' => 'U',
            'Ф' => 'F', 'Х' => 'Kh', 'Ц' => 'Ts', 'Ч' => 'Ch', 'Ш' => 'Sh', 'Щ' => 'Shch',
            'Ь' => '', 'Ю' => 'Iu', 'Я' => 'Ia'
        ];
        
        return strtr($text, $cyrillicToLatin);
    }

    public static function addHeaderIds(string $html): string
    {
        return preg_replace_callback(
            '/<h([1-6])>(.*?)<\/h\1>/',
            function ($matches) {
                $level = $matches[1];
                $text = $matches[2];
                
                // Транслітерація для кирилиці
                $id = self::transliterate($text);
                $id = strtolower(trim($id));
                $id = preg_replace('/[^a-z0-9\s-]/', '', $id);
                $id = preg_replace('/\s+/', '-', $id);
                $id = trim($id, '-');
                
                return "<h{$level} id=\"{$id}\">{$text}</h{$level}>";
            },
            $html
        );
    }
}
