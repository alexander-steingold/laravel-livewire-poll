<?php

namespace App\Http\Livewire;

use App\Models\Poll;
use Livewire\Component;

class CreatePoll extends Component
{
    public $title;
    public $options = [''];

    protected $rules = [
        'title' => 'required|min:3|max:255',
        'options' => 'required|array|min:1|max:10',
        'options.*' => 'required|min:3|max:255',
    ];

    protected $messages = [
        'options.*' => 'The option cannot be empty.',
        'options.*.min' => 'The option must  be at least 3 characters.',
    ];


    public function render()
    {
        return view('livewire.create-poll');
    }

    public function addOption()
    {
        $this->options[] = '';
    }

    public function removeOption($index)
    {
        unset($this->options[$index]);
        $this->options = array_values($this->options);
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function createPoll()
    {
        $this->validate();
//        $poll = Poll::create([
//            'title' => $this->title,
//        ]);
//
//        foreach ($this->options as $option) {
//            $poll->options()->create(['name' => $option]); //insert to <options> table and associate with poll
//        }

        //---------------- refactoring saving data

        Poll::create([
            'title' => $this->title,
        ])->options()->createMany( // access to <options> relationship
            collect($this->options) // convert to laravel collection object
            ->map(fn($option) => ['name' => $option]) // extract each option
            ->all() // convert results to array
        );

        $this->reset(['title', 'options']);

        $this->emit('pollCreated');
    }
//    public function mount()
//    {
//
//    }
}
