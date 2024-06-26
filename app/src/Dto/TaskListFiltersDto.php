<?php
/**
 * Task list filters DTO.
 */

namespace App\Dto;

use App\Entity\Category;

/**
 * Class TaskListFiltersDto.
 */
class TaskListFiltersDto
{
    /**
     * Constructor.
     *
     * @param Category|null $category   Category entity
     */
    public function __construct(public readonly ?Category $category)
    {
    }
}