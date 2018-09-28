<?
declare(strict_types=1);

namespace App;

interface PHTMLContext
{

    public function getLocalizedTexts(): LocalizedTexts;

    public function getLanguageCode(): string;

}