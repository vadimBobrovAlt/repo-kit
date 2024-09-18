<?php

namespace bobrovva\repo_kit\SelectField;

use Illuminate\Database\Eloquent\Builder;

/**
 * Класс SelectFieldBuilder
 *
 * Построитель запросов для выборки полей из таблицы с поддержкой вложенных отношений (relations).
 * Позволяет настраивать выборку полей и связанных данных на основе допустимых полей.
 */
class SelectFieldBuilder
{
    /**
     * Строит запрос для выборки указанных полей и отношений, если они разрешены.
     *
     * @param Builder $query Экземпляр запроса (Query Builder).
     * @param array $fields Массив полей, которые необходимо выбрать.
     * @param array $allowedFields Массив разрешённых полей, включая связи (relations).
     * @return Builder Возвращает модифицированный запрос с учётом выборки полей и отношений.
     */
    public static function build(Builder $query, array $fields, array $allowedFields = []): Builder
    {
        // Если поля не заданы, возвращаем исходный запрос
        if (empty($fields)) {
            return $query;
        }

        $selectFields = []; // Поля для выборки
        $withFields = [];   // Поля для отношений

        foreach ($fields as $field) {

            // Если поле является массивом, значит это связь с другой моделью
            if (is_array($field)) {

                // Проверяем, разрешено ли это поле
                if (!isset($allowedFields[$field['field']])) {
                    continue;
                }

                $withFieldList = [];

                // Если у поля есть дочерние элементы (связанные данные)
                if (isset($field['children']) && !empty($field['children'])) {

                    // Перебираем дочерние поля, разрешённые для данного отношения
                    $allowedFieldsKeys = array_keys($allowedFields[$field['field']]);
                    foreach ($field['children'] as $childField) {
                        if (!in_array($childField, $allowedFieldsKeys)) {
                            continue;
                        }
                        $withFieldList[] = $childField;
                    }

                    // Добавляем ключи отношений (relation_key), если они есть
                    foreach ($allowedFields[$field['field']] as $withFieldItem) {
                        if (isset($withFieldItem['relation_key'])) {
                            $withFieldList[] = $withFieldItem['field'];
                        }
                    }

                    // Если поле не содержит дочерних элементов, обрабатываем его как обычное отношение
                } elseif (isset($field['field'])) {
                    foreach ($allowedFields[$field['field']] as $withFieldItem) {
                        if (isset($withFieldItem['relation'])) {
                            $withFieldList[] = $withFieldItem['field'];
                        }
                    }
                }

                // Если список полей для отношения не пуст, добавляем их в запрос через with()
                if (!empty($withFieldList)) {
                    $withFields[$field['field']] = fn($q) => $q->select($withFieldList);
                }

                // Если поле не является отношением, обрабатываем как обычное поле
            } else {

                // Проверяем, разрешено ли это поле
                if (!isset($allowedFields[$field])) {
                    continue;
                }

                // Если поле является массивом (имеет вложенные связи), обрабатываем их
                if (is_array($allowedFields[$field])) {
                    $withFieldList = [];
                    foreach ($allowedFields[$field] as $withFieldItem) {
                        if (isset($withFieldItem['relation'])) {
                            $withFieldList[] = $withFieldItem['field'];
                        }
                    }
                    if (!empty($withFieldList)) {
                        $withFields[$field] = fn($q) => $q->select($withFieldList);
                    }
                }

                // Добавляем поле в список для выборки
                $allowedField = $allowedFields[$field];
                if (isset($allowedField['field'])) {
                    $selectFields[] = $allowedField['field'];
                }
            }
        }

        // Если список полей для выборки не пуст, добавляем их в запрос
        if (!empty($selectFields)) {
            $query->select($selectFields);
        }

        // Если есть отношения для подгрузки, добавляем их через with()
        if (!empty($withFields)) {
            $query->with($withFields);
        }

        return $query;
    }
}