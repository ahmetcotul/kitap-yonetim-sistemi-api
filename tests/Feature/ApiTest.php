<?php

use App\Models\Book;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('can create a new book', function () {
    $response = $this->postJson('/api/books', [
        'title' => 'Test Book',
        'author' => 'Test Author',
        'description' => 'This is a test book description.',
    ]);

    // Check if the response status is 201 (created)
    $response->assertStatus(201);

    // Ensure the returned book data is correct
    $book = $response->json();
    expect($book['title'])->toBe('Test Book');
    expect($book['author'])->toBe('Test Author');
    expect($book['description'])->toBe('This is a test book description.');
});

it('can retrieve a list of books', function () {
    // Create some books in the database
    Book::factory()->count(3)->create();

    // Send a GET request to retrieve all books
    $response = $this->getJson('/api/books');

    // Check if the response status is 200
    $response->assertStatus(200);

    // Ensure the response contains multiple books
    expect(count($response->json()))->toBeGreaterThanOrEqual(3);
});

it('can retrieve a single book by id', function () {
    // Create a book in the database
    $book = Book::factory()->create();

    // Send a GET request to retrieve the book by its ID
    $response = $this->getJson("/api/books/{$book->id}");

    // Check if the response status is 200
    $response->assertStatus(200);

    // Ensure the response contains the correct book data
    expect($response->json('title'))->toBe($book->title);
    expect($response->json('author'))->toBe($book->author);
});

it('returns 404 when trying to retrieve a non-existing book', function () {
    // Attempt to retrieve a book with a non-existent ID
    $response = $this->getJson('/api/books/999999');

    // Check if the response status is 404
    $response->assertStatus(404);

    // Ensure the response contains the correct message
    $response->assertJson(['message' => 'Book not found']);
});

it('can update an existing book', function () {
    // Create a book in the database
    $book = Book::factory()->create();

    // Send a PUT request to update the book
    $response = $this->putJson("/api/books/{$book->id}", [
        'title' => 'Updated Book Title',
        'author' => 'Updated Author',
        'description' => 'Updated description.',
    ]);

    // Check if the response status is 200
    $response->assertStatus(200);

    // Ensure the updated book data is correct
    $updatedBook = $response->json();
    expect($updatedBook['title'])->toBe('Updated Book Title');
    expect($updatedBook['author'])->toBe('Updated Author');
    expect($updatedBook['description'])->toBe('Updated description.');
});

it('returns 404 when trying to update a non-existing book', function () {
    // Attempt to update a book with a non-existent ID
    $response = $this->putJson('/api/books/999999', [
        'title' => 'Updated Book Title',
        'author' => 'Updated Author',
    ]);

    // Check if the response status is 404
    $response->assertStatus(404);

    // Ensure the response contains the correct message
    $response->assertJson(['message' => 'Book not found']);
});

it('can delete a book', function () {
    $book = Book::factory()->create();

    // Send a DELETE request to delete the book
    $response = $this->deleteJson("/api/books/{$book->id}");

    // Check if the response status is 200
    $response->assertStatus(200);

    // Ensure the deletion message is correct
    $response->assertJson(['message' => 'Book deleted Successfully']);

    // Verify that the book has been deleted from the database
    $this->assertDatabaseMissing('books', ['id' => $book->id]);
});

it('returns 404 when trying to delete a non-existing book', function () {
    // Attempt to delete a book with a non-existent ID
    $response = $this->deleteJson('/api/books/999999');

    // Check if the response status is 404
    $response->assertStatus(404);

    // Ensure the response contains the correct message
    $response->assertJson(['message' => 'Book not found']);
});
