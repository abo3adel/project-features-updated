<?php

use App\Models\Todo;
use App\Models\User;

test('any one can not add new todo', function () {
    [$user, $cat, $proj] = userWithTodos();

    actingAs()
        ->post(route('tasks.store', [$cat->slug, $proj->slug]), [])
        ->assertForbidden();
});

test('project owner can not add task with invalid data', function () {
    [$user, $cat, $proj] = userWithTodos();

    actingAs($user)
        ->postJson(route('tasks.store', [$cat->slug, $proj->slug]), [])
        ->assertStatus(422);
});

test('project owner can add task', function () {
    [$user, $cat, $proj] = userWithTodos();

    $task = Todo::factory()->raw();

    actingAs($user)
        ->postJson(route('tasks.store', [$cat->slug, $proj->slug]), $task)
        ->assertCreated()
        ->assertJson(['body' => $task['body']]);

    expect(Todo::whereBody($task['body'])->exists())->toBeTrue();
});

test('invited user can add task', function () {
    [$user, $cat, $proj] = userWithTodos();
    $invited = User::factory()->create();

    $proj->addToTeam($invited);

    $task = Todo::factory()->raw();

    actingAs($invited)
        ->postJson(route('tasks.store', [$cat->slug, $proj->slug]), $task)
        ->assertCreated()
        ->assertJson(['body' => $task['body']]);

    expect(Todo::whereBody($task['body'])->exists())->toBeTrue();
});

test('user can not update task with invalid data', function () {
    [$user, $cat, $proj, $task] = userWithTodos();

    actingAs($user)
        ->putJson(route('tasks.update', [$cat->slug, $proj->slug, $task->id]), [
            'body' => '',
        ])
        ->assertStatus(422);

    expect(Todo::find($task->id))->body->toBe($task->body);
});

test('project owner can update task', function () {
    [$user, $cat, $proj, $task] = userWithTodos();

    $body = fake()->sentence;

    actingAs($user)
        ->putJson(
            route('tasks.update', [$cat->slug, $proj->slug, $task->id]),
            compact('body')
        )
        ->assertNoContent();

    expect(Todo::find($task->id))->body->toBe($body);
});

test('project team user can update task', function () {
    [, $cat, $proj, $task] = userWithTodos();
    $ali = User::factory()->create();
    $body = fake()->sentence;

    actingAs($ali)
        ->putJson(
            route('tasks.update', [$cat->slug, $proj->slug, $task->id]),
            compact('body')
        )
        ->assertForbidden();
    expect(Todo::find($task->id))->body->toBe($task->body);

    $proj->addToTeam($ali);

    actingAs($ali)
        ->putJson(
            route('tasks.update', [$cat->slug, $proj->slug, $task->id]),
            compact('body')
        )
        ->assertNoContent();

    expect(Todo::find($task->id))->body->toBe($body);
});

test('project owner can delete task', function () {
    [$user, $cat, $proj, $task] = userWithTodos();

    expect(Todo::find($task->id)->exists())->toBeTrue();

    actingAs($user)
        ->delete(route('tasks.destroy', [$cat->slug, $proj->slug, $task->id]))
        ->assertNoContent();

    expect(Todo::find($task->id)?->exists())->toBeNull();
    $this->assertDatabaseMissing('todos', ['id' => $task->id]);
});
