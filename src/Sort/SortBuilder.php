<?php

namespace bobrovva\repo_kit\Sort;

use Illuminate\Database\Eloquent\Builder;

/**
 * Класс SortBuilder
 *
 * Построитель сортировки для запросов. Позволяет добавлять сортировку по указанным полям и направлениям (asc/desc) в запросах.
 */
class SortBuilder
{
    /**
     * Строит запрос с сортировкой на основе указанных полей и разрешённых полей для сортировки.
     *
     * @param Builder $query Экземпляр запроса (Query Builder).
     * @param array $defaultSorts Массив полей по умолчанию для сортировки.
     * @param array $allowedSorts Массив разрешённых полей для сортировки.
     * @return Builder Возвращает модифицированный запрос с учётом сортировки.
     */
    public static function build(Builder $query, array $defaultSorts = [], array $allowedSorts = []): Builder
    {
        // Получаем параметры сортировки из запроса или используем поля по умолчанию
        $sortFields = request()->get('sort', $defaultSorts);

        // Перебираем поля сортировки и добавляем их к запросу
        foreach ($sortFields as $field) {

            $sortFields = self::preparedSortField($field);

            // Проверяем, разрешено ли поле для сортировки
            if (!in_array($sortFields['field_name'], $allowedSorts)) {
                continue;
            }

            // Добавляем сортировку по полю
            $query->orderBy($sortFields['field_name'], $sortFields['symbol']);
        }

        return $query;
    }

    /**
     * Подготавливает поле для сортировки, определяя направление сортировки (asc/desc).
     *
     * @param string $field Строка, представляющая поле для сортировки.
     * @return array Массив с подготовленными параметрами сортировки, включая название поля и направление сортировки.
     */
    protected static function preparedSortField(string $field): array
    {
        // Если поле начинается с '-', сортировка по убыванию (desc)
        if (str_contains($field, '-')) {
            return [
                'symbol' => 'desc',
                'field_name' => substr($field, 1)
            ];
        } else {
            // Иначе сортировка по возрастанию (asc)
            return [
                'symbol' => 'asc',
                'field_name' => $field
            ];
        }
    }
}