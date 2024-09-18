<?php

namespace bobrovva\repo_kit\Repositories\Traits;

use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

trait PaginateRepositoryTrait
{
    /**
     * Количество элементов на одной странице по умолчанию.
     *
     * @var int
     */
    private int $perPage = 25;

    /**
     * Устанавливает значение по умолчанию для количества элементов на странице.
     *
     * @return int Значение по умолчанию для количества элементов на странице
     */
    protected function setDefaultPerPage(): int
    {
        return $this->perPage;
    }

    /**
     * Применяет пагинацию к запросу на основе параметров запроса.
     *
     * Параметры запроса:
     * - `per_page`: Количество элементов на странице.
     * - `page`: Номер страницы для обычной пагинации.
     * - `cursor`: Параметр для пагинации с использованием курсора.
     *
     * @param Builder $query Построитель запроса Eloquent
     * @return array|Collection|CursorPaginator|LengthAwarePaginator Результат запроса с примененной пагинацией
     */
    protected function setPaginate(Builder $query): array|Collection|CursorPaginator|LengthAwarePaginator
    {
        $perPage = request()->get('per_page', $this->perPage);

        if (request()->exists('page')) {
            return $query->paginate($perPage);
        } elseif (request()->exists('cursor')) {
            return $query->cursorPaginate($perPage);
        } else {
            return $query->get();
        }
    }
}
