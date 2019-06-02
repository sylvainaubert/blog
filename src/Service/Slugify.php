<?php


namespace App\Service;


class Slugify
{

    public function generate(string $input): string
    {
        setlocale(LC_ALL, 'fr_FR');
        $slug = mb_strtolower($input);
        $slug = trim($slug);
        $slug = iconv('UTF-8', 'ASCII//TRANSLIT', $slug);
        $slug = trim(mb_strtolower($slug));
        $slug = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $slug);
        $slug = preg_replace("/[\/_|+ -]+/", '-', $slug);
        return $slug;
    }
}
