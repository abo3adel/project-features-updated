<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Project;
use App\Models\Todo;
use Blade;
use DB;
use Doctrine\DBAL\Query\QueryBuilder;
use GuzzleHttp\Handler\Proxy;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use View;

class TodoController extends Controller
{
    const VALIDATION_RULES = [
        'body' => 'required|string|min:1|max:255',
    ];

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Category $category, Project $project)
    {
        $tasks = Todo::where('project_id', $project->id)
            ->orderByDesc('updated_at')
            ->simplePaginate();

        if (request()->wantsJson()) {
            return response()->json(compact('tasks'));
        }

        return view('task.index', compact('category', 'project'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(
        Request $request,
        Category $category,
        Project $project
    ) {
        $task = $project
            ->todos()
            ->create($request->validate(self::VALIDATION_RULES));

        $task->completed = false;
        return response()->json($task, 201);
    }

    /**
     * toggle the state of task
     *
     * @param Request $request
     * @param Category $category
     * @param Project $project
     * @param Todo $task
     * @return void
     */
    public function toggle(
        Request $request,
        Category $category,
        Project $project,
        Todo $task
    ) {
        /**
         * this should not go to observe
         * because we do not to run this check 
         * for normal task body update
         * bu instead for the completed state toggle only
         */

        ['completed' => $completed] = $request->validate([
            'completed' => 'required|boolean',
        ]);

        $task->updateQuietly(compact('completed'));
        
        // determine if all project tasks was completed or not
        $tasks = Todo::whereProjectId($project->id);
        $checked = $task->whereCompleted(true);
        if ($tasks->count() === $checked->count()) {
            // update project state to completed
            $project->update(['completed' => true]);
        } else {
            // check if project was completed
            if ($project->completed) {
                // then update to not completed
                $project->update(['completed' => false]);
            }
        }

        return response()->noContent();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Todo  $task
     * @return \Illuminate\Http\Response
     */
    public function update(
        Request $request,
        Category $category,
        Project $project,
        Todo $task
    ) {
        $task->update($request->validate(self::VALIDATION_RULES));

        return response()->noContent();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Todo  $task
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category, Project $project, Todo $task)
    {
        $task->delete();

        return response()->noContent();
    }
}
