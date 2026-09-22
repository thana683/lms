<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">สร้างหลักสูตร</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('courses.store') }}">
                    @csrf
                    @include('courses._form')

                    <div class="mt-6 flex justify-end gap-2">
                        <a href="{{ route('courses.index') }}" class="px-4 py-2 text-sm text-gray-600">ยกเลิก</a>
                        <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-md text-sm">สร้างหลักสูตร</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
