<?php

namespace bobrovva\repo_kit\Filter;

use Illuminate\Database\Eloquent\Builder;

/**
 * Class CustomFilter
 *
 * Применяет пользовательские фильтры к запросу на основе конфигурации разрешенных фильтров.
 */
class CustomFilter
{
    /**
     * Применяет пользовательский фильтр к запросу.
     *
     * Находит и использует класс фильтра, соответствующий модели, указанной в конфигурации разрешенных фильтров.
     *
     * @param Builder $query Запрос, к которому применяется фильтр
     * @param array $allowedFilter Конфигурация разрешенных фильтров
     * @param mixed $value Значение для применения фильтра
     * @return Builder Модифицированный запрос
     */
    public function build(Builder $query, array $allowedFilter, $value): Builder
    {
        // Формирует полный путь к классу пользовательского фильтра на основе имени модели
        $classPath = 'Models/Custom';

        // Construct the full class name
        $class = "{$classPath}\\{$allowedFilter['model']}CustomFilter";

        // Проверяет, существует ли указанный класс
        if (class_exists($class)) {
            // Создает экземпляр класса и вызывает его метод build для применения фильтра
            $query = (new $class())->build($query, $allowedFilter, $value);
        }

        return $query;
    }
}