<?php

use App\Models\Todo;

test('todo belongs to project', function () {
    $todo = Todo::factory()->createQuietly();

    expect($todo->project)->not->toBeNull();
});
