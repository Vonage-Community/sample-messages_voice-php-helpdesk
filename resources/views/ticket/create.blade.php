<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
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
                        <form method="POST" action="{{ route('ticket.create') }}">
                            @csrf
                                <label for="title">Title</label>
                                <input type="text" id="title" name="title">
                                <label for="content">Content</label>
                                <input type="text" id="content" name="content">
                                <label for="recipient">Recipient</label>
                                <input type="text" id="recipient" name="recipient">
                                <div class="radio">
                                    <label>
                                        <input type="radio" name="channel" value="sms">
                                        SMS
                                    </label>
                                </div>
                                <div class="radio">
                                    <label>
                                        <input type="radio" name="channel" value="voice">
                                        Voice
                                    </label>
                                </div>
                                <input type="submit">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
