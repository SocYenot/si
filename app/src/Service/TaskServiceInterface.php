<?php
/**
 * Task service interface.
 */

namespace App\Service;

use App\Entity\Task;
use Knp\Component\Pager\Pagination\PaginationInterface;
use App\Entity\User;
use App\Dto\TaskListInputFiltersDto;

/**
 * Interface TaskServiceInterface.
 */
interface TaskServiceInterface
{
    /**
     * Get paginated list.
     *
     * @param int                     $page    Page number
     *
     * @param User                    $author  Author
     * @param TaskListInputFiltersDto $filters Filters
     *
     * @return PaginationInterface<string, mixed> Paginated list
     */
    public function getPaginatedList(int $page, User $author, TaskListInputFiltersDto $filters): PaginationInterface;

    /**
     * Save entity.
     *
     * @param Task $task Task entity
     */
    public function save(Task $task): void;

    /**
     * Delete entity.
     *
     * @param Task $task Task entity
     */
    public function delete(Task $task): void;
}
