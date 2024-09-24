<?php

namespace bobrovva\repo_kit\Filter;

use Illuminate\Database\Eloquent\Builder;
/**
 * Abstract class FilterContract
 *
 * Определяет контракт для построения фильтров, которые могут применяться к запросам.
 * Содержит логику для вызова метода фильтрации на основе конфигурации фильтра.
 */
abstract class FilterContract
{
    /**
     * Массив методов фильтрации, сопоставленных с действиями.
     *
     * @var array
     */
    protected array $methodsMap;

    /**
     * Применяет фильтр к запросу.
     *
     * В зависимости от конфигурации фильтра и переданных значений, вызывает соответствующий метод фильтрации.
     *
     * @param Builder $query Запрос, к которому применяется фильтр
     * @param array $allowedFilter Конфигурация разрешенного фильтра
     * @param mixed $value Значение фильтра или массив значений
     * @return Builder Модифицированный запрос
     */
    public function build(Builder $query, array $allowedFilter, $value): Builder
    {
        // Определяет функцию запроса по умолчанию
        $queryFunction = $allowedFilter['query_function'] ?? 'where';

        // Вызывает соответствующий метод фильтрации на основе карты методов
        return call_user_func(
            [$this, $this->methodsMap[$queryFunction]],
            $query,
            $value,
            $allowedFilter
        );
    }
}