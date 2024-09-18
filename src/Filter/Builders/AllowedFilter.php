<?php

namespace bobrovva\repo_kit\Filter\Builders;

/**
 * Класс AllowedFilter
 *
 * Помогает создавать фильтры для запросов, позволяя легко настраивать действия фильтрации (например, `where`, `in`, `like`, и т.д.).
 */
class AllowedFilter
{
    /**
     * Префикс, используемый для фильтров.
     */
    const prefix = 'base';

    /**
     * Создает фильтр с использованием оператора `where`.
     *
     * @param string $requestField Имя поля запроса для фильтрации.
     * @param string|null $dbField Имя поля в базе данных, которое будет использоваться для фильтрации. Если не указано, используется $requestField.
     * @return array Массив с настройками фильтра.
     */
    public static function where(string $requestField, ?string $dbField = null): array
    {
        return self::setAction($requestField, 'where', $dbField);
    }

    /**
     * Создает фильтр с использованием оператора `whereIn`.
     *
     * @param string $requestField Имя поля запроса для фильтрации.
     * @param string|null $dbField Имя поля в базе данных, которое будет использоваться для фильтрации. Если не указано, используется $requestField.
     * @return array Массив с настройками фильтра.
     */
    public static function whereIn(string $requestField, ?string $dbField = null): array
    {
        return self::setAction($requestField, 'in', $dbField);
    }

    /**
     * Создает фильтр с использованием оператора `whereLike`.
     *
     * @param string $requestField Имя поля запроса для фильтрации.
     * @param string|null $dbField Имя поля в базе данных, которое будет использоваться для фильтрации. Если не указано, используется $requestField.
     * @return array Массив с настройками фильтра.
     */
    public static function whereLike(string $requestField, ?string $dbField = null): array
    {
        return self::setAction($requestField, 'like', $dbField);
    }

    /**
     * Создает фильтр с использованием функции `whereDateFunc`.
     *
     * @param string $requestField Имя поля запроса для фильтрации.
     * @param string|null $dbField Имя поля в базе данных, которое будет использоваться для фильтрации. Если не указано, используется $requestField.
     * @return array Массив с настройками фильтра.
     */
    public static function whereDateFunc(string $requestField, ?string $dbField = null): array
    {
        return self::setAction($requestField, 'date', $dbField);
    }

    /**
     * Устанавливает действия фильтрации для указанного поля запроса.
     *
     * @param string $requestField Имя поля запроса для фильтрации.
     * @param string $action Действие фильтрации (например, `where`, `in`, `like`, `date`).
     * @param string|null $dbField Имя поля в базе данных, которое будет использоваться для фильтрации. Если не указано, используется $requestField.
     * @return array Массив с настройками фильтра.
     */
    protected static function setAction(string $requestField, string $action, ?string $dbField = null): array
    {
        if (!$dbField) {
            $dbField = $requestField;
        }

        return [
            $requestField => [
                'prefix' => self::prefix,
                'field' => $dbField,
                'query_function' => $action
            ]
        ];
    }
}
