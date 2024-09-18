<?php

namespace bobrovva\repo_kit\Filter;

use Illuminate\Database\Eloquent\Builder;

/**
 * Class FilterBuilder
 *
 * Служит для применения различных типов фильтров к запросу на основе предоставленных фильтров и их конфигураций.
 */
class FilterBuilder
{
    /**
     * Применяет фильтры к запросу.
     *
     * Создает и применяет фильтры на основе конфигурации и значения фильтра.
     *
     * @param Builder $query Запрос, к которому применяются фильтры
     * @param array $filters Массив фильтров, где ключ - имя фильтра, а значение - значение фильтра
     * @param array $allowedFilters Массив разрешенных фильтров, где ключ - имя фильтра, а значение - конфигурация фильтра
     * @return Builder Модифицированный запрос
     */
    public static function build(Builder $query, array $filters, array $allowedFilters = []): Builder
    {
        // Если фильтры не предоставлены, возвращаем исходный запрос
        if (empty($filters)) {
            return $query;
        }

        // Создаем экземпляры классов фильтров
        $baseFilterClass = new BaseFilter;
        $relationFilterClass = new RelationFilter;
        $customFilterClass = new CustomFilter;

        // Применяем каждый фильтр к запросу
        foreach ($filters as $key => $value) {

            // Пропускаем фильтры, которые не разрешены
            if (!isset($allowedFilters[$key])) {
                continue;
            }

            // Получаем конфигурацию разрешенного фильтра
            $allowedFilter = $allowedFilters[$key];

            // Применяем соответствующий фильтр в зависимости от префикса
            switch ($allowedFilter['prefix']) {
                case 'base':
                    $query = $baseFilterClass->build($query, $allowedFilter, $value);
                    break;
                case 'relation':
                    $query = $relationFilterClass->build($query, $allowedFilter, $value);
                    break;
                case 'custom':
                    $query = $customFilterClass->build($query, $allowedFilter, $value);
                    break;
            }
        }

        return $query;
    }
}
