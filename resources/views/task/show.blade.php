<div class="flex items-center justify-between my-1 p-2 card-bg">
    <div x-on:click.prevent="complete(task)">
        <input x-bind:checked="task.completed" x-bind:id="'checkbox' + task.id" type="checkbox"
            class="w-4 h-4 text-green-600 bg-gray-100 rounded border-gray-300 focus:ring-green-500 dark:focus:ring-green-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600" x-show="toggle !== task.id" required/>
            <x-btn-spinner color="text-green-600 !m-0" x-show="toggle === task.id" />
            <label x-bind:for="'checkbox' + task.id" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">
            <span x-text="task.body"></span>
        </label>
    </div>
    <div>
        <button class="btn cyan my-1" type="button" aria-describedby="edit task"
            x-on:click.prevent="$dispatch('edit-task', task)">
            <x-fas-pencil />
            <span class="hidden lg:inline-block">
                {{ __('category.edit') }}
            </span>
        </button>
        <x-btn-with-spinner icon="fas-trash" desc="task delete" busy="deleting == task.id" class="red my-1"
            x-on:click.prevent="remove(task.id)">
            <span class="hidden lg:inline-block">
                {{ __('category.delete') }}
            </span>
        </x-btn-with-spinner>
    </div>
</div>
