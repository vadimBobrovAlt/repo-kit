<?php

namespace bobrovva\repo_kit\Repositories\Traits;

use Illuminate\Database\Eloquent\Builder;

trait SoftDeleteTrait
{
    /**
     * Флаг, указывающий, нужно ли включать удаленные записи.
     *
     * @var bool
     */
    private bool $withTrashed = false;

    /**
     * Флаг, указывающий, нужно ли возвращать только удаленные записи.
     *
     * @var bool
     */
    private bool $onlyTrashed = false;

    /**
     * Устанавливает флаг для включения удаленных записей в результаты запроса.
     *
     * @return $this
     */
    public function withTrashed(): self
    {
        $this->withTrashed = true;
        return $this;
    }

    /**
     * Устанавливает флаг для возвращения только удаленных записей в результатах запроса.
     *
     * @return $this
     */
    public function onlyTrashed(): self
    {
        $this->onlyTrashed = true;
        return $this;
    }

    /**
     * Применяет фильтрацию по удаленным записям к запросу.
     *
     * Если установлен флаг $withTrashed, то в запрос включаются удаленные записи.
     * Если установлен флаг $onlyTrashed, то в запрос включаются только удаленные записи.
     *
     * @param Builder $query Построитель запроса Eloquent
     * @return Builder Модифицированный построитель запроса
     */
    protected function setSoftDelete(Builder $query): Builder
    {
        if ($this->withTrashed) {
            $query = $query->withTrashed();
        }

        if ($this->onlyTrashed) {
            $query = $query->onlyTrashed();
        }

        return $query;
    }
}