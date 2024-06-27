<?php
/**
 * Task service.
 */

namespace App\Service;

use App\Entity\Task;
use App\Repository\TaskRepository;
use App\Entity\User;
use Symfony\Component\Security\Core\User\UserInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use App\Dto\TaskListFiltersDto;
use App\Dto\TaskListInputFiltersDto;

/**
 * Class TaskService.
 */
class TaskService implements TaskServiceInterface
{
    /**
     * Items per page.
     *
     * Use constants to define configuration options that rarely change instead
     * of specifying them in app/config/config.yml.
     * See https://symfony.com/doc/current/best_practices.html#configuration
     *
     * @constant int
     */
    private const PAGINATOR_ITEMS_PER_PAGE = 10;

    /**
     * Constructor.
     *
     *
     * @param CategoryServiceInterface $categoryService Category service
     * @param TaskRepository           $taskRepository  Task repository
     * @param PaginatorInterface       $paginator       Paginator
     *
     */
    public function __construct(private readonly CategoryServiceInterface $categoryService, private readonly TaskRepository $taskRepository, private readonly PaginatorInterface $paginator)
    {
    }

    /**
     * Get paginated list.
     *
     *@param int                     $page    Page number
     *@param User                    $author  Tasks author
     *@param TaskListInputFiltersDto $filters Filters
     *
     *@return PaginationInterface<string, mixed> Paginated list
 */
    public function getPaginatedList(int $page, UserInterface $author, TaskListInputFiltersDto $filters): PaginationInterface
    {
        $filters = $this->prepareFilters($filters);

        if ($author->getRoles() && in_array('ROLE_ADMIN', $author->getRoles(), true)) {
            return $this->paginator->paginate(
                $this->taskRepository->queryAll($filters),
                $page,
                self::PAGINATOR_ITEMS_PER_PAGE
            );
        }

        return $this->paginator->paginate(
            $this->taskRepository->queryByAuthor($author, $filters),
            $page,
            self::PAGINATOR_ITEMS_PER_PAGE
        );
    }

    /**
     * Save entity.
     *
     * @param Task $task Task entity
     */
    public function save(Task $task): void
    {
        $this->taskRepository->save($task);
    }

    /**
     * Delete entity.
     *
     * @param Task $task Task entity
     */
    public function delete(Task $task): void
    {
        $this->taskRepository->delete($task);
    }

    /**
     * Prepare filters for the tasks list.
     *
     * @param TaskListInputFiltersDto $filters Raw filters from request
     *
     * @return TaskListFiltersDto Result filters
     */
    private function prepareFilters(TaskListInputFiltersDto $filters): TaskListFiltersDto
    {
        return new TaskListFiltersDto(
            null !== $filters->categoryId ? $this->categoryService->findOneById($filters->categoryId) : null,
        );
    }
}
