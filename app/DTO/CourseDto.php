<?php

namespace App\DTO;

class CourseDto
{
    public string $course_name;
    public string $description;
    public float $rating = 0.0;
    public float $price;
    public string $status = 'pending_approval';
    public bool $is_paid;
    public string $start_date;
    public string $end_date;
    public int $category_id;
    public string $poster;

    public static function fromArray(array $data): self
    {
        $dto = new self();
        $dto->course_name = $data['course_name'];
        $dto->description = $data['description'];
        $dto->rating = 0.0;
        $dto->price = $data['price'] ?? 0.0;
        $dto->status = 'pending_approval';
        $dto->is_paid = $data['is_paid'];
        $dto->start_date = $data['start_date'];
        $dto->end_date = $data['end_date'];
        $dto->category_id = $data['category_id'];
        $dto->poster = $data['poster'];
        return $dto;
    }
}
