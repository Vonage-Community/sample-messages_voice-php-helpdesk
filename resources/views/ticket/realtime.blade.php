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
                    <div class="panel-heading"># {{ $ticket->id }} / {{ $ticket->status }} / {{ $ticket->title }}</div>
                    <div class="panel-body">
                        Realtime Messaging
                    </div>

                        <div class="panel-body">
                        </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
