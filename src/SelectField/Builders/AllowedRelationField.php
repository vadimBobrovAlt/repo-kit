<?php

namespace bobrovva\repo_kit\SelectField\Builders;

/**
 * Класс AllowedRelationField
 *
 * Предоставляет утилиту для создания списка полей с учетом связанных моделей (relations).
 * Позволяет встраивать поля таблицы с учетом их принадлежности к связям.
 */
class AllowedRelationField
{
    /**
     * Встраивает имена полей с учетом отношений (relations), создавая массив с ключами полей и их алиасами.
     *
     * @param string $table Название основной таблицы.
     * @param string $relation Имя отношения (связи) с другой моделью.
     * @param array $fields Массив полей, которые нужно встроить.
     * @param string|null $relationTable Название таблицы, связанной через отношение.
     * @param string|null $relationId Имя столбца для связи с моделью (например, внешний ключ).
     * @param string|null $withId Идентификатор родителя, используемый для связи.
     * @return array Возвращает ассоциативный массив с полями и метаданными о связи.
     */
    /*
       $fields = AllowedRelationField::embed(
          'users',       // Основная таблица
          'profile',     // Название отношения
          ['name', 'age'], // Поля, которые нужно встроить
          'profiles',    // Таблица отношения
          'profile_id',  // Столбец для связи
          'user_id'      // ID родителя
       );

        Возвращаемое значение:
        [
            [
                'profile_id' => ['field' => 'profiles.profile_id']
            ],
            [
                'profile' => [
                    'name' => ['field' => 'users.name', 'relation' => true],
                    'age' => ['field' => 'users.age', 'relation' => true],
                    'user_id' => ['field' => 'users.user_id', 'relation' => true, 'relation_key' => true]
                ]
            ]
        ]
    */
    public static function embed(
        string  $table,
        string  $relation,
        array   $fields,
        ?string $relationTable = null,
        ?string $relationId = null, // Столбец для связи с моделью
        ?string $withId = null // ID родителя
    ): array
    {
        // Если передан один аргумент в виде массива, используем его как список полей
        if (count($fields) === 1 && is_array($fields[0])) {
            $fields = $fields[0];
        }

        $fieldList = []; // Итоговый список полей
        $fieldItem = []; // Поля для текущего отношения

        // Встраиваем поля для текущего отношения
        foreach ($fields as $field) {
            $fieldItem[$relation][$field] = [
                'field' => "{$table}.{$field}", // Поле с указанием таблицы
                'relation' => true // Указание, что это связано с отношением
            ];
        }

        // Если задан relationId, добавляем идентификатор связи
        if ($relationId) {
            $fieldItem[$relation][$withId] = [
                'field' => "{$table}.{$withId}",
                'relation' => true,
                'relation_key' => true // Указание, что это ключ связи
            ];
        }

        // Если задан relationId, добавляем поле для связи с relationTable
        if ($relationId) {
            $fieldList[][$relationId] = [
                'field' => "{$relationTable}.{$relationId}",
            ];
        }

        // Добавляем поля отношений в итоговый список
        $fieldList[] = $fieldItem;

        return $fieldList;
    }
}