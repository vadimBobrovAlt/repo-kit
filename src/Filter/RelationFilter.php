<?php

namespace bobrovva\repo_kit\Filter;

use Illuminate\Database\Eloquent\Builder;
/**
 * Class RelationFilter
 *
 * Применяет фильтры к отношениям в запросах Eloquent.
 * Использует методы фильтрации для применения условий на связанные модели.
 */
class RelationFilter extends FilterContract
{
    /**
     * Массив методов фильтрации, сопоставленных с действиями.
     *
     * @var array
     */
    protected array $methodsMap = [
        'where' => 'whereFunc',
        'in' => 'whereInFunc',
        'nin' => 'whereNotInFunc',
        'like' => 'whereLikeFunc',
        'lt' => 'whereLtFunc',
        'gt' => 'whereGtFunc',
        'lte' => 'whereLteFunc',
        'gte' => 'whereGteFunc',
        'eq' => 'whereEqFunc',
        'neq' => 'whereNeqFunc',
    ];

    /**
     * Применяет условие "где" к связанным моделям.
     *
     * @param Builder $query Запрос, к которому применяется фильтр
     * @param mixed $value Значение фильтра
     * @param array $allowedEmbed Конфигурация фильтра
     * @return Builder Модифицированный запрос
     */
    public function whereFunc(Builder $query, $value, array $allowedEmbed): Builder
    {
        $operator = $allowedEmbed['operator'] ?? '=';
        return $query->whereHas($allowedEmbed['relation'], function ($query) use ($allowedEmbed, $value, $operator) {
            $query->where($allowedEmbed['field'], $operator, $value);
        });
    }

    /**
     * Применяет условие "похожие" к связанным моделям.
     *
     * @param Builder $query Запрос, к которому применяется фильтр
     * @param string $value Значение фильтра
     * @param array $allowedEmbed Конфигурация фильтра
     * @return Builder Модифицированный запрос
     */
    public function whereLikeFunc(Builder $query, string $value, array $allowedEmbed): Builder
    {
        return $query->whereHas($allowedEmbed['relation'], function ($query) use ($allowedEmbed, $value) {
            $query->where($allowedEmbed['field'], 'like', '%' . $value . '%');
        });
    }

    /**
     * Применяет условие "в" к связанным моделям.
     *
     * @param Builder $query Запрос, к которому применяется фильтр
     * @param mixed $value Значение фильтра
     * @param array $allowedEmbed Конфигурация фильтра
     * @return Builder Модифицированный запрос
     */
    public function whereInFunc(Builder $query, $value, array $allowedEmbed): Builder
    {
        $value = is_array($value) ? $value : explode(';', $value);
        return $query->whereHas($allowedEmbed['relation'], function ($query) use ($allowedEmbed, $value) {
            $query->whereIn($allowedEmbed['field'], $value);
        });
    }

    /**
     * Применяет условие "не в" к связанным моделям.
     *
     * @param Builder $query Запрос, к которому применяется фильтр
     * @param mixed $value Значение фильтра
     * @param array $allowedEmbed Конфигурация фильтра
     * @return Builder Модифицированный запрос
     */
    public function whereNotInFunc(Builder $query, $value, array $allowedEmbed): Builder
    {
        $value = is_array($value) ? $value : explode(';', $value);
        return $query->whereHas($allowedEmbed['relation'], function ($query) use ($allowedEmbed, $value) {
            $query->whereNotIn($allowedEmbed['field'], $value);
        });
    }

    /**
     * Применяет условие "больше чем" к связанным моделям.
     *
     * @param Builder $query Запрос, к которому применяется фильтр
     * @param mixed $value Значение фильтра
     * @param array $allowedEmbed Конфигурация фильтра
     * @return Builder Модифицированный запрос
     */
    public function whereGtFunc(Builder $query, $value, array $allowedEmbed): Builder
    {
        return $query->whereHas($allowedEmbed['relation'], function ($query) use ($allowedEmbed, $value) {
            $query->where($allowedEmbed['field'], '>', $value);
        });
    }

    /**
     * Применяет условие "меньше чем" к связанным моделям.
     *
     * @param Builder $query Запрос, к которому применяется фильтр
     * @param mixed $value Значение фильтра
     * @param array $allowedEmbed Конфигурация фильтра
     * @return Builder Модифицированный запрос
     */
    public function whereLtFunc(Builder $query, $value, array $allowedEmbed): Builder
    {
        return $query->whereHas($allowedEmbed['relation'], function ($query) use ($allowedEmbed, $value) {
            $query->where($allowedEmbed['field'], '<', $value);
        });
    }

    /**
     * Применяет условие "больше или равно" к связанным моделям.
     *
     * @param Builder $query Запрос, к которому применяется фильтр
     * @param mixed $value Значение фильтра
     * @param array $allowedEmbed Конфигурация фильтра
     * @return Builder Модифицированный запрос
     */
    public function whereGteFunc(Builder $query, $value, array $allowedEmbed): Builder
    {
        return $query->whereHas($allowedEmbed['relation'], function ($query) use ($allowedEmbed, $value) {
            $query->where($allowedEmbed['field'], '>=', $value);
        });
    }

    /**
     * Применяет условие "меньше или равно" к связанным моделям.
     *
     * @param Builder $query Запрос, к которому применяется фильтр
     * @param mixed $value Значение фильтра
     * @param array $allowedEmbed Конфигурация фильтра
     * @return Builder Модифицированный запрос
     */
    public function whereLteFunc(Builder $query, $value, array $allowedEmbed): Builder
    {
        return $query->whereHas($allowedEmbed['relation'], function ($query) use ($allowedEmbed, $value) {
            $query->where($allowedEmbed['field'], '<=', $value);
        });
    }

    /**
     * Применяет условие "равно" к связанным моделям.
     *
     * @param Builder $query Запрос, к которому применяется фильтр
     * @param mixed $value Значение фильтра
     * @param array $allowedEmbed Конфигурация фильтра
     * @return Builder Модифицированный запрос
     */
    public function whereEqFunc(Builder $query, $value, array $allowedEmbed): Builder
    {
        return $query->whereHas($allowedEmbed['relation'], function ($query) use ($allowedEmbed, $value) {
            $query->where($allowedEmbed['field'], '=', $value);
        });
    }

    /**
     * Применяет условие "не равно" к связанным моделям.
     *
     * @param Builder $query Запрос, к которому применяется фильтр
     * @param mixed $value Значение фильтра
     * @param array $allowedEmbed Конфигурация фильтра
     * @return Builder Модифицированный запрос
     */
    public function whereNeqFunc(Builder $query, $value, array $allowedEmbed): Builder
    {
        return $query->whereHas($allowedEmbed['relation'], function ($query) use ($allowedEmbed, $value) {
            $query->where($allowedEmbed['field'], '!=', $value);
        });
    }
}
