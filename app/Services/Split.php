<?php

namespace App\Services;

class Split
{

    /**
     * Список ключей ботов
     * @var array
     */
    private static array $agents = [
        'Google' => [
            'Googlebot', 'Googlebot-Image', 'Mediapartners-Google', 'AdsBot-Google', 'APIs-Google',
            'AdsBot-Google-Mobile', 'AdsBot-Google-Mobile', 'Googlebot-News', 'Googlebot-Video',
            'AdsBot-Google-Mobile-Apps',
        ],
        'Yandex' => [
            'YandexBot', 'YandexAccessibilityBot', 'YandexMobileBot', 'YandexDirectDyn', 'YandexScreenshotBot',
            'YandexImages', 'YandexVideo', 'YandexVideoParser', 'YandexMedia', 'YandexBlogs', 'YandexFavicons',
            'YandexWebmaster', 'YandexPagechecker', 'YandexImageResizer', 'YandexAdNet', 'YandexDirect',
            'YaDirectFetcher', 'YandexCalendar', 'YandexSitelinks', 'YandexMetrika', 'YandexNews',
            'YandexNewslinks', 'YandexCatalog', 'YandexAntivirus', 'YandexMarket', 'YandexVertis',
            'YandexForDomain', 'YandexSpravBot', 'YandexSearchShop', 'YandexMedianaBot', 'YandexOntoDB',
            'YandexOntoDBAPI', 'YandexTurbo', 'YandexVerticals',
        ],
        'Bing' => [
            'bingbot',
        ],
        'Baidu' => [
            'Baiduspider',
        ],
    ];

    /**
     * Разбивает строку на массив по шаблону
     * @param string $pattern Шаблон поиска
     * @param string $row Входная строка
     * @return array            Рузультат поиска шаблона в строке
     */
    public
    static function splitByPattern(string $pattern, string $row): array
    {

        $patternIsMatchesSubject = preg_match($pattern, $row, $matches);

        return $patternIsMatchesSubject === 1 ? $matches : [];
    }

    /**
     * Getter текущей записи лога в виде ассоциативного массива
     * @return array     Тукущая запись файла логов
     */
    public
    static function lineSplit(string $row): array
    {
        $lineKeys = [];

        if ($row !== '') {
            $pattern = '/(\S+) (\S+) (\S+) \[(.+?)\] \"(\S+) (.*?) (\S+)\" (\S+) (\S+) \"(.*?)\" \"(.*?)\"/';

            $lineParameters = self::splitByPattern($pattern, $row);

            if (count($lineParameters) === 12) {
                $lineKeys = [
                    'ip' => $lineParameters[1],
                    'identity' => $lineParameters[2],
                    'user' => $lineParameters[3],
                    'date' => $lineParameters[4],
                    'method' => $lineParameters[5],
                    'path' => $lineParameters[6],
                    'protocol' => $lineParameters[7],
                    'status' => $lineParameters[8],
                    'bytes' => $lineParameters[9],
                    'referer' => $lineParameters[10],
                    'agent' => $lineParameters[11],
                ];
            }
        }

        return $lineKeys;
    }

    /**
     * Получаем наименование поискового Бота
     * @param string $userAgent user_agent
     * @return string             Наименование бота
     */
    public static function agentName(string $userAgent): ?string
    {

        if ($userAgent === '') {
            return null;
        }

        foreach (self::$agents as $agent => $agentsKeys) {
            foreach ($agentsKeys as $agentKey) {
                if (self::strrpos($userAgent, $agentKey) !== null) {
                    return $agent;
                }
            }
        }
        return null;
    }

    /**
     * Возвращает позицию последнего вхождения подстроки в строке
     * @param string $string Входная строка.
     * @param string $agentKey Искомая подстрока
     * @param int|null $offset Если равно или больше ноля, то поиск будет идти слева направо и, при этом, будут пропущены первые offset байт строки. Если меньше ноля, то поиск будет идти справа налево. При этом будут отброшены offset байт с конца
     * @return int               Возвращает номер позиции последнего вхождения needle относительно начала строки
     */
    public static function strrpos(string $string, string $agentKey, int $offset = null): ?int
    {
        $result = is_null($offset) ? strrpos($string, $agentKey) : strrpos($string, $agentKey, $offset);

        return $result === false ? null : $result;
    }

    public function debug($array)
    {
        echo "\n" . print_r($array, true) . "\n";
    }

}
