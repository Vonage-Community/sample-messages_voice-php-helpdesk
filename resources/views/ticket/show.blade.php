<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>
    <a href="{{ route('ticket.index') }}"><< Back</a>
    <div class="py-3 px-8">
        <div class="container">
            <div class="row">
                <div class="panel panel-default">
                    <div class="panel-heading"># {{ $ticket->id }} / {{ $ticket->status }} / {{ $ticket->notification_method }} / {{ $ticket->title }}</div>
                    <div class="panel-body">
                        @forelse ($ticket->entries as $entry)
                            <strong>
                                {{ $entry->user->email }} /
                                {{ $entry->channel }} /
                                {{ $entry->created_at->diffForHumans() }}
                            </strong>
                            <p>{{ $entry->content }}</p>
                            <hr />
                        @empty
                            <p>No entries found</p>
                        @endforelse
                    </div>

                        <div class="panel-body">
                            <form action={{ route('ticket.update', $ticket->id) }} method="POST" id="add-reply">
                                @csrf
                                <div class="form-group">
                                    <label for="reply">Add a reply</label>
                                    <textarea name="content" id="reply"></textarea>
                                    <input type="hidden" name="channel" value="web">
                                </div>
                                <input type="submit">
                            </form>
                        </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
