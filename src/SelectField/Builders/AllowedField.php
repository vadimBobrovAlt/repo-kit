<?php

namespace bobrovva\repo_kit\SelectField\Builders;

/**
 * Класс AllowedField
 *
 * Предоставляет утилиту для объединения полей таблицы с их алиасами.
 */
class AllowedField
{
    /**
     * Встраивает имена полей в таблицу, создавая массив с ключами полей и значениями, содержащими алиасы.
     *
     * @param string $table Название таблицы, к которой относятся поля.
     * @param mixed ...$fields Список полей или массив полей.
     * @return array Ассоциативный массив, где ключи — это имена полей, а значения — массивы с ключом 'field' и значением имени поля с таблицей.
     */
    public static function embed(string $table, ...$fields): array
    {
        // Если передан один аргумент и он является массивом, используем его как список полей
        if (count($fields) === 1 && is_array($fields[0])) {
            $fields = $fields[0];
        }

        // Создаем ассоциативный массив, где ключи — имена полей, а значения — массивы с полем field и именем поля
        return array_combine($fields, array_map(fn($field) => ['field' => "{$table}.{$field}"], $fields));
    }
}
