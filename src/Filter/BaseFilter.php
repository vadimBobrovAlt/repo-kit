<?php

namespace bobrovva\repo_kit\Filter;

use Illuminate\Database\Eloquent\Builder;
/**
 * Класс BaseFilter
 *
 * Предоставляет базовый набор методов для фильтрации запросов в базе данных.
 * Методы используют различные операторы и функции для построения запросов.
 */
class BaseFilter extends FilterContract
{
    /**
     * Карта методов фильтрации и соответствующих функций.
     *
     * @var array
     */
    protected array $methodsMap = [
        'where' => 'whereFunc',
        'null' => 'whereNullFunc',
        'in' => 'whereInFunc',
        'nin' => 'whereNotInFunc',
        'like' => 'whereLikeFunc',
        'lt' => 'whereLtFunc',
        'gt' => 'whereGtFunc',
        'lte' => 'whereLteFunc',
        'gte' => 'whereGteFunc',
        'eq' => 'whereEqFunc',
        'neq' => 'whereNeqFunc',
        'date' => 'whereDateFunc',
        'and' => 'whereAndFunc',
        'or' => 'whereOrFunc',
        'not' => 'whereNotFunc',
    ];

    /**
     * Применяет условие "где" к запросу.
     *
     * @param Builder $query Запрос
     * @param mixed $value Значение для сравнения
     * @param array $allowedEmbed Параметры для фильтра
     * @return Builder Объект запроса с примененным условием
     */
    public function whereFunc(Builder $query, $value, array $allowedEmbed): Builder
    {
        $operator = $allowedEmbed['operator'] ?? '=';
        return $query->where($allowedEmbed['field'], $operator, $value);
    }

    /**
     * Применяет условие "где NULL" к запросу.
     *
     * @param Builder $query Запрос
     * @param mixed $value Значение (не используется)
     * @param array $allowedEmbed Параметры для фильтра
     * @return Builder Объект запроса с примененным условием
     */
    public function whereNullFunc(Builder $query, $value, array $allowedEmbed): Builder
    {
        return $query->whereNull($allowedEmbed['field']);
    }

    /**
     * Применяет условие "дата" к запросу.
     *
     * @param Builder $query Запрос
     * @param mixed $value Значение даты
     * @param array $allowedEmbed Параметры для фильтра
     * @return Builder Объект запроса с примененным условием
     */
    public function whereDateFunc(Builder $query, $value, array $allowedEmbed): Builder
    {
        $operator = $allowedEmbed['operator'] ?? '=';
        $value = ($value instanceof \Carbon\Carbon) ? $value : \Carbon\Carbon::parse($value);
        return $query->whereDate($allowedEmbed['field'], $operator, $value);
    }

    /**
     * Применяет условие "похожее на" к запросу.
     *
     * @param Builder $query Запрос
     * @param mixed $value Значение для сравнения
     * @param array $allowedEmbed Параметры для фильтра
     * @return Builder Объект запроса с примененным условием
     */
    public function whereLikeFunc(Builder $query, $value, array $allowedEmbed): Builder
    {
        return $query->where($allowedEmbed['field'], 'like', '%' . $value . '%');
    }

    /**
     * Применяет условие "в списке" к запросу.
     *
     * @param Builder $query Запрос
     * @param mixed $value Значение для сравнения
     * @param array $allowedEmbed Параметры для фильтра
     * @return Builder Объект запроса с примененным условием
     */
    public function whereInFunc(Builder $query, $value, array $allowedEmbed): Builder
    {
        $value = is_array($value) ? $value : explode(';', $value);
        return $query->whereIn($allowedEmbed['field'], $value);
    }

    /**
     * Применяет условие "не в списке" к запросу.
     *
     * @param Builder $query Запрос
     * @param mixed $value Значение для сравнения
     * @param array $allowedEmbed Параметры для фильтра
     * @return Builder Объект запроса с примененным условием
     */
    public function whereNotInFunc(Builder $query, $value, array $allowedEmbed): Builder
    {
        $value = is_array($value) ? $value : explode(';', $value);
        return $query->whereNotIn($allowedEmbed['field'], $value);
    }

    /**
     * Применяет условие "больше чем" к запросу.
     *
     * @param Builder $query Запрос
     * @param mixed $value Значение для сравнения
     * @param array $allowedEmbed Параметры для фильтра
     * @return Builder Объект запроса с примененным условием
     */
    public function whereGtFunc(Builder $query, $value, array $allowedEmbed): Builder
    {
        return $query->where($allowedEmbed['field'], '>', $value);
    }

    /**
     * Применяет условие "меньше чем" к запросу.
     *
     * @param Builder $query Запрос
     * @param mixed $value Значение для сравнения
     * @param array $allowedEmbed Параметры для фильтра
     * @return Builder Объект запроса с примененным условием
     */
    public function whereLtFunc(Builder $query, $value, array $allowedEmbed): Builder
    {
        return $query->where($allowedEmbed['field'], '<', $value);
    }

    /**
     * Применяет условие "больше или равно" к запросу.
     *
     * @param Builder $query Запрос
     * @param mixed $value Значение для сравнения
     * @param array $allowedEmbed Параметры для фильтра
     * @return Builder Объект запроса с примененным условием
     */
    public function whereGteFunc(Builder $query, $value, array $allowedEmbed): Builder
    {
        return $query->where($allowedEmbed['field'], '>=', $value);
    }

    /**
     * Применяет условие "меньше или равно" к запросу.
     *
     * @param Builder $query Запрос
     * @param mixed $value Значение для сравнения
     * @param array $allowedEmbed Параметры для фильтра
     * @return Builder Объект запроса с примененным условием
     */
    public function whereLteFunc(Builder $query, $value, array $allowedEmbed): Builder
    {
        return $query->where($allowedEmbed['field'], '<=', $value);
    }

    /**
     * Применяет условие "равно" к запросу.
     *
     * @param Builder $query Запрос
     * @param mixed $value Значение для сравнения
     * @param array $allowedEmbed Параметры для фильтра
     * @return Builder Объект запроса с примененным условием
     */
    public function whereEqFunc(Builder $query, $value, array $allowedEmbed): Builder
    {
        return $query->where($allowedEmbed['field'], '=', $value);
    }

    /**
     * Применяет условие "не равно" к запросу.
     *
     * @param Builder $query Запрос
     * @param mixed $value Значение для сравнения
     * @param array $allowedEmbed Параметры для фильтра
     * @return Builder Объект запроса с примененным условием
     */
    public function whereNeqFunc(Builder $query, $value, array $allowedEmbed): Builder
    {
        return $query->where($allowedEmbed['field'], '!=', $value);
    }
}