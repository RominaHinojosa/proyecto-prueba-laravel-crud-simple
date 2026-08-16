<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD de Tareas - Laravel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
        };
    </script>
    <script>
        (function () {
            const stored = localStorage.getItem('theme');
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            const isDark = stored ? stored === 'dark' : prefersDark;
            document.documentElement.classList.toggle('dark', isDark);
        })();
    </script>
</head>
<body class="bg-gray-100 dark:bg-gray-900 min-h-screen py-10 flex items-center justify-center transition-colors">
    <button id="theme-toggle" type="button" aria-label="Alternar modo oscuro"
        class="fixed top-4 right-4 p-2 rounded-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 shadow hover:bg-gray-50 dark:hover:bg-gray-700 transition">
        <svg id="theme-toggle-sun-icon" class="hidden w-5 h-5 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
            <path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 9a1 1 0 100 2h1a1 1 0 100-2h-1zM4.464 4.343a1 1 0 011.414 0l.707.707A1 1 0 105.171 6.464l-.707-.707a1 1 0 010-1.414zM3 9a1 1 0 000 2H2a1 1 0 000-2h1zm2.171 6.536a1 1 0 001.414 1.414l.707-.707a1 1 0 00-1.414-1.414l-.707.707zM10 16a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1z" />
        </svg>
        <svg id="theme-toggle-moon-icon" class="hidden w-5 h-5 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
            <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z" />
        </svg>
    </button>

    <div class="bg-white dark:bg-gray-800 p-8 rounded-xl shadow-lg w-full max-w-xl transition-colors">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white mb-6 text-center">Mi Lista de Tareas</h1>

        <!-- Contador de tareas -->
        <div class="flex justify-center gap-4 mb-6">
            <span class="px-4 py-2 rounded-lg bg-green-100 dark:bg-green-900/40 text-green-700 dark:text-green-300 font-semibold text-sm">
                Completadas: {{ $tasks->where('completed', true)->count() }}
            </span>
            <span class="px-4 py-2 rounded-lg bg-yellow-100 dark:bg-yellow-900/40 text-yellow-700 dark:text-yellow-300 font-semibold text-sm">
                Pendientes: {{ $tasks->where('completed', false)->count() }}
            </span>
        </div>

        <!-- Formulario de creación -->
        <form action="{{ route('tasks.store') }}" method="POST" enctype="multipart/form-data" class="mb-6 space-y-3">
            @csrf
            <div>
                <input type="text" name="title" placeholder="Título de la tarea..." required
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400">
            </div>
            <div>
                <input type="text" name="description" placeholder="Descripción breve (opcional)..."
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400">
            </div>
            <div>
                <select name="category_id"
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <option value="">Sin categoría</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <select name="priority"
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <option value="baja">Baja</option>
                    <option value="media" selected>Media</option>
                    <option value="alta">Alta</option>
                </select>
            </div>
            <div>
                <input type="file" name="attachment"
                    class="w-full text-sm text-gray-600 dark:text-gray-300 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-gray-600 dark:file:text-gray-100 dark:hover:file:bg-gray-500">
            </div>
            <button type="submit"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 rounded-lg transition">
                Agregar Tarea
            </button>
        </form>

        <!-- Barra de búsqueda y filtros -->
        <form action="{{ route('tasks.index') }}" method="GET" class="mb-6 p-4 bg-gray-50 dark:bg-gray-700/50 border dark:border-gray-600 rounded-lg space-y-3">
            <div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar por título o descripción..."
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400">
            </div>
            <div class="flex gap-2">
                <select name="category_id"
                    class="flex-1 px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <option value="">Todas las categorías</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected(request('category_id') == $category->id)>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                <select name="status"
                    class="flex-1 px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <option value="all" @selected(request('status', 'all') === 'all')>Todas</option>
                    <option value="pending" @selected(request('status') === 'pending')>Solo Pendientes</option>
                    <option value="completed" @selected(request('status') === 'completed')>Solo Completadas</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit"
                    class="flex-1 bg-gray-700 hover:bg-gray-800 dark:bg-gray-600 dark:hover:bg-gray-500 text-white font-semibold py-2 rounded-lg transition">
                    Filtrar
                </button>
                <a href="{{ route('tasks.index') }}"
                    class="flex-1 text-center bg-gray-200 hover:bg-gray-300 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-700 dark:text-gray-200 font-semibold py-2 rounded-lg transition">
                    Limpiar filtros
                </a>
            </div>
        </form>

        <!-- Lista de tareas -->
        <div class="space-y-3">
            @forelse ($tasks as $task)
                <div class="flex items-center justify-between p-4 border rounded-lg {{ $task->completed ? 'bg-green-50 border-green-200 dark:bg-green-900/20 dark:border-green-800' : 'bg-gray-50 dark:bg-gray-700 dark:border-gray-600' }}">
                    <div class="flex-1">
                        <h3 class="font-semibold {{ $task->completed ? 'line-through text-gray-400 dark:text-gray-500' : 'text-gray-800 dark:text-white' }}">
                            {{ $task->title }}
                        </h3>
                        <div class="mt-1 flex flex-wrap gap-1">
                            @if ($task->category)
                                <span class="inline-block px-2 py-0.5 text-xs rounded-full bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300 font-medium">
                                    {{ $task->category->name }}
                                </span>
                            @endif
                            <span @class([
                                'inline-block px-2 py-0.5 text-xs rounded-full font-medium',
                                'bg-emerald-100 dark:bg-emerald-900/40 text-emerald-800 dark:text-emerald-300' => $task->priority === 'baja',
                                'bg-amber-100 dark:bg-amber-900/40 text-amber-800 dark:text-amber-300' => $task->priority === 'media',
                                'bg-rose-100 dark:bg-rose-900/40 text-rose-800 dark:text-rose-300' => $task->priority === 'alta',
                            ])>
                                {{ ucfirst($task->priority) }}
                            </span>
                        </div>
                        @if ($task->description)
                            <p class="text-sm text-gray-500 dark:text-gray-400 {{ $task->completed ? 'line-through' : '' }}">{{ $task->description }}</p>
                        @endif
                        @if ($task->attachment)
                            <a href="{{ \Illuminate\Support\Facades\Storage::url($task->attachment) }}" target="_blank" rel="noopener noreferrer"
                                class="inline-block mt-1 text-xs text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 underline font-medium">
                                Ver archivo
                            </a>
                        @endif
                    </div>
                    <div class="flex items-center space-x-2">
                        <!-- Botón Completar / Pendiente -->
                        <form action="{{ route('tasks.update', $task) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="px-3 py-1 text-xs rounded font-medium {{ $task->completed ? 'bg-yellow-500 text-white' : 'bg-green-600 text-white' }}">
                                {{ $task->completed ? 'Desmarcar' : 'Completar' }}
                            </button>
                        </form>

                        <!-- Botón Eliminar -->
                        <form action="{{ route('tasks.destroy', $task) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-3 py-1 text-xs bg-red-600 hover:bg-red-700 text-white rounded font-medium">
                                Eliminar
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <p class="text-center text-gray-400 dark:text-gray-500 py-4">No hay tareas creadas todavía.</p>
            @endforelse
        </div>
    </div>

    <script>
        (function () {
            const html = document.documentElement;
            const toggleBtn = document.getElementById('theme-toggle');
            const sunIcon = document.getElementById('theme-toggle-sun-icon');
            const moonIcon = document.getElementById('theme-toggle-moon-icon');

            function updateIcons() {
                const isDark = html.classList.contains('dark');
                sunIcon.classList.toggle('hidden', !isDark);
                moonIcon.classList.toggle('hidden', isDark);
            }

            updateIcons();

            toggleBtn.addEventListener('click', function () {
                const isDark = html.classList.toggle('dark');
                localStorage.setItem('theme', isDark ? 'dark' : 'light');
                updateIcons();
            });
        })();
    </script>
</body>
</html>