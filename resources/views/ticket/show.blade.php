<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="container">
            <div class="row">
                <div class="panel panel-default">
                    <div class="panel-heading"># {{ $ticket->id }} / {{ $ticket->status }} / {{ $ticket->channel }} / {{ $ticket->title }}</div>
                    <div class="panel-body">
                        @forelse ($ticket->entries as $entry)
                            <strong>
                                {{ $entry->user->email }} /
                                {{ $entry->created_at->diffForHumans() }}
                            </strong>
                            <p>{{ $entry->content }}</p>
                            <hr />
                        @empty
                            <p>No entries found</p>
                        @endforelse
                    </div>

                    @if ($ticket)
                        <div class="panel-body">
                            <form action=""  method="POST" id="add-reply">
                                <div class="form-group">
                                    <label for="reply">Add a reply</label>
                                    <textarea class="form-control" id="reply" rows="3"></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary mb-2" style="display:none;" id="reply-submit">Save</button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
