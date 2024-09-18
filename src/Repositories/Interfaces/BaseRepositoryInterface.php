<?php

namespace bobrovva\repo_kit\Repositories\Interfaces;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;

interface BaseRepositoryInterface
{
    /**
     * Получает запрос с возможными атрибутами (фильтрация, сортировка, выборка полей и т.д.)
     *
     * @param array $attributes
     * @return Builder
     */
    public function getQuery(array $attributes = []): Builder;

    /**
     * Получает списка данных
     *
     */
    public function get();

    /**
     * Получает одну запись по ID или первую запись
     *
     * @param string|null $id
     * @return Model|null
     */
    public function one(?string $id = null): ?Model;

    /**
     * Получает одну запись по ID или фильтрам иначе выбрасывает исключение, если запись не найдена
     *
     * @param string|null $id
     * @return Model
     */
    public function oneOrFail(?string $id = null): Model;

    /**
     * Создает новую запись в базе данных
     *
     * @param array $data
     * @return Model
     */
    public function create(array $data): Model;

    /**
     * Обновляет существующую запись
     *
     * @param string|Model $model
     * @param array $data
     * @return Model|null
     */
    public function update(string|Model $model, array $data): ?Model;

    /**
     * Удаляет запись
     *
     * @param string|Model $model
     * @return bool
     */
    public function delete(string|Model $model): bool;

    /**
     * Проверяет существование записей
     *
     * @return bool
     */
    public function exists(): bool;

    /**
     * Возвращает количество записей
     *
     * @return int
     */
    public function count(): int;

    /**
     * Обновляет или создает запись
     *
     * @param array $attributes
     * @param array $values
     * @return Model
     */
    public function updateOrCreate(array $attributes, array $values = []): Model;

    /**
     * Устанавливает сортировку для запроса
     *
     * @param array $sorts
     * @return self
     */
    public function setQuerySorts(array $sorts): self;

    /**
     * Устанавливает фильтры для запроса
     *
     * @param array $filters
     * @return self
     */
    public function setQueryFilters(array $filters): self;

    /**
     * Включает удаленные записи в запрос
     *
     * @return self
     */
    public function withTrashed(): self;

    /**
     * Устанавливает запрос только для удаленных записей
     *
     * @return self
     */
    public function onlyTrashed(): self;

    /**
     * Находит первую запись с указанными атрибутами или создает новую запись, если она не найдена
     *
     * @param array $attributes Атрибуты для поиска записи
     * @param array $values Дополнительные значения для создания записи, если она не найдена
     * @return Model
     */
    public function firstOrCreate(array $attributes = [], array $values = []): Model;

    /**
     * Выполняет массовую вставку нескольких записей в базу данных
     *
     * @param array $data Массив данных для вставки
     * @return bool Возвращает true, если вставка прошла успешно
     */
    public function bulkInsert(array $data): bool;

    /**
     * Выполняет "сырой" SQL запрос и возвращает коллекцию результатов
     *
     * @param string $query SQL-запрос
     * @return Collection Коллекция результатов запроса
     */
    public function rawQuery(string $query): Collection;

    /**
     * Восстанавливает удаленную запись по ID
     *
     * @param string $id Идентификатор записи
     * @return bool Возвращает true, если запись успешно восстановлена
     */
    public function restore(string $id): bool;

    /**
     * Принудительно удаляет запись по ID (без возможности восстановления)
     *
     * @param string $id Идентификатор записи
     * @return bool Возвращает true, если запись была успешно удалена
     */
    public function forceDelete(string $id): bool;

    /**
     * Выполняет массовое обновление записей с помощью фильтров и данных для обновления
     *
     * @param array $filters Условия для поиска записей, которые нужно обновить
     * @param array $data Массив данных для обновления записей
     * @return bool Возвращает true, если обновление прошло успешно
     */
    public function bulkUpdate(array $filters, array $data): bool;
}
