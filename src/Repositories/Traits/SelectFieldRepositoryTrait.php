<?php

namespace bobrovva\repo_kit\Repositories\Traits;

use bobrovva\repo_kit\SelectField\SelectFieldBuilder;
use Illuminate\Database\Eloquent\Builder;

trait SelectFieldRepositoryTrait
{
    /** @var array Список разрешенных полей для выборки */
    private array $allowedFields = [];

    /** @var array Список полей по умолчанию */
    private array $defaultFields = [];

    /** @var array Список полей в запросе */
    private array $queryFields = [];

    /**
     * Возвращает список разрешенных полей для выборки.
     *
     * @return array
     */
    protected function setAllowedFields(): array
    {
        return $this->allowedFields;
    }

    /**
     * Возвращает список полей по умолчанию.
     *
     * @return array
     */
    protected function setDefaultFields(): array
    {
        return $this->defaultFields;
    }

    /**
     * Устанавливает поля, которые будут использоваться в запросе.
     *
     * @param array $fields Массив полей для выборки в запросе.
     * @return self Возвращает текущий экземпляр для цепочечных вызовов.
     */
    public function setQueryFields(array $fields): self
    {
        $this->queryFields = $fields;
        return $this;
    }

    /**
     * Устанавливает поля для выборки в запросе.
     *
     * Если указаны поля в параметре $fields, они будут добавлены в список полей.
     * Если в запросе присутствует параметр 'embed', он будет добавлен к списку полей.
     * Если в запросе присутствует параметр 'extended', он будет объединен с полями по умолчанию.
     * В противном случае будут использованы поля по умолчанию.
     *
     * @param Builder $query
     * @param string|null $fields
     * @return Builder
     */
    protected function setSelectField(Builder $query, ?string $fields = ''): Builder
    {
        $fieldList = [];

        if (!empty($fields)) {
            $fieldList = explode(',', $fields);
        }

        $defaultFields = $this->queryFields;
        if (empty($defaultFields)){
            $defaultFields = $this->setDefaultFields();
        }

        if (request() && request()->has('embed') && !empty(request()->has('embed'))) {
            $fieldList = array_merge($fieldList, $this->parseFields(request()->get('embed', '')));
        } elseif (request() && request()->has('extended') && !empty(request()->has('extended'))) {
            if (empty($defaultFields)){
                $defaultFields = $this->allowedFields;
            }

            $fieldList = array_merge(
                $fieldList,
                $this->parseFields(request()->get('extended', '')),
                $defaultFields
            );
        } else {
            $fieldList = array_merge($fieldList, $defaultFields);
        }

        if (empty($fieldList)) {
            $fieldList = $this->allowedFields;
        }

        $allowedFields = collect($this->setAllowedFields())->collapse()->toArray();

        return SelectFieldBuilder::build($query, $fieldList, $allowedFields);
    }

    /**
     * Разбирает строку полей и возвращает массив структурированных полей.
     *
     * Пример строки: "field1;[field2;[field3];field4];field5"
     * Результат: [['field1'], ['field2', ['field3'], 'field4'], 'field5']
     *
     * @param string $input Строка полей
     * @return array Структурированный массив полей
     */
    function parseFields(string $input): array
    {
        $result = [];
        $current = '';
        $stack = [&$result];

        for ($i = 0, $length = strlen($input); $i < $length; $i++) {
            $char = $input[$i];

            if ($char === ';') {
                if ($current !== '') {
                    $stack[count($stack) - 1][] = $current;
                    $current = '';
                }
            } elseif ($char === '[') {
                if ($current !== '') {
                    $newGroup = [
                        'field' => $current,
                        'children' => []
                    ];
                    $stack[count($stack) - 1][] = $newGroup;
                    $stack[] = &$stack[count($stack) - 1][count($stack[count($stack) - 1]) - 1]['children'];
                    $current = '';
                }
            } elseif ($char === ']') {
                if ($current !== '') {
                    $stack[count($stack) - 1][] = $current;
                    $current = '';
                }
                array_pop($stack);
            } else {
                $current .= $char;
            }
        }

        if ($current !== '') {
            $stack[count($stack) - 1][] = $current;
        }

        return $result;
    }

}
