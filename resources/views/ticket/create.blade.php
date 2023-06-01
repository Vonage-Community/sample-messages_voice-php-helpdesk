<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12 px-8">
        <div class="container">
            <div class="row">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div class="panel-heading">Create Ticket</div>

                    <div class="panel-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <form method="POST" action="{{ route('ticket.create') }}" class="w-full">
                            @csrf
                                <fieldset>
                                    <label for="title">Title</label>
                                    <input type="text" id="title" name="title">
                                    <label for="content">Content</label>
                                    <input type="text" id="content" name="content">
                                    <input type="hidden" id="channel" name="channel" value="web">
                                </fieldset>
                                <fieldset>
                                    <label for="isConversation">In-App Messaging</label>
                                    <input type="checkbox" id="isConversation" name="isConversation">
                                </fieldset>
                                <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                    <input type="submit">
                                </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
