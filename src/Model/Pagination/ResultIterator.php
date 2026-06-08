<?php

declare(strict_types=1);

namespace AvitoAds\Model\Pagination;

/**
 * Помощник для последовательного обхода всех страниц list-метода.
 *
 * Принимает функцию-загрузчик страницы и отдаёт сущности по одной через генератор,
 * автоматически увеличивая номер страницы, пока не будут получены все записи.
 * Это избавляет вызывающий код от ручного управления пагинацией.
 */
final class ResultIterator
{
    /**
     * Перебирает все сущности по всем страницам.
     *
     * @param callable(ListRequest): PaginatedResult<mixed> $loader   загрузчик одной страницы
     * @param ListRequest                             $request  начальные параметры (страница будет инкрементироваться)
     *
     * @return \Generator<int, mixed>
     */
    public static function iterate(callable $loader, ListRequest $request): \Generator
    {
        $page = $request->getPage();

        do {
            /** @var PaginatedResult<mixed> $result */
            $result = $loader($request->withPage($page));
            $items = $result->getItems();

            foreach ($items as $item) {
                yield $item;
            }

            $page++;
            // Останавливаемся, когда страница пустая или собраны все записи.
        } while ($items !== [] && $result->hasNextPage());
    }
}
